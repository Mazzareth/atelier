<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConversationController extends Controller
{
    protected function conversationListFor(Request $request)
    {
        $user = $request->user();
        $search = trim((string) $request->query('q', ''));

        return Conversation::with(['userOne.profileModules', 'userTwo.profileModules', 'messages.user', 'commissionRequest'])
            ->where(function ($query) use ($user) {
                $query->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->when($search !== '', function ($query) use ($search, $user) {
                $searchLower = strtolower($search);
                $query->where(function ($sub) use ($searchLower, $user) {
                    $sub->whereRaw("LOWER(COALESCE(title, '')) LIKE ?", ["%{$searchLower}%"])
                        ->orWhereHas('userOne', function ($userQuery) use ($searchLower, $user) {
                            $userQuery->where('id', '!=', $user->id)
                                ->where(function ($nameQuery) use ($searchLower) {
                                    $nameQuery->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
                                        ->orWhereRaw('LOWER(username) LIKE ?', ["%{$searchLower}%"]);
                                });
                        })
                        ->orWhereHas('userTwo', function ($userQuery) use ($searchLower, $user) {
                            $userQuery->where('id', '!=', $user->id)
                                ->where(function ($nameQuery) use ($searchLower) {
                                    $nameQuery->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
                                        ->orWhereRaw('LOWER(username) LIKE ?', ["%{$searchLower}%"]);
                                });
                        });
                });
            })
            ->latest('updated_at')
            ->get();
    }

    public function index(Request $request): View
    {
        $conversations = $this->conversationListFor($request);

        return view('conversations.index', [
            'conversations' => $conversations,
            'activeConversation' => null,
            'otherParty' => null,
            'commissionRequest' => null,
            'isCommissionConversation' => false,
            'isArtistView' => false,
            'searchQuery' => (string) $request->query('q', ''),
        ]);
    }

    public function start(Request $request, string $username): RedirectResponse
    {
        $otherUser = User::whereRaw('LOWER(username) = ?', [strtolower($username)])->firstOrFail();
        $user = $request->user();

        abort_if($otherUser->id === $user->id, 422, 'You cannot start a conversation with yourself.');

        $conversation = Conversation::between($user->id, $otherUser->id)
            ->where('kind', 'direct')
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $user->id,
                'user_two_id' => $otherUser->id,
                'kind' => 'direct',
                'title' => null,
                'user_one_last_read_at' => now(),
                'user_two_last_read_at' => null,
            ]);
        }

        return redirect()->route('conversations.show', $conversation);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $user = $request->user();
        abort_unless($conversation->includesUser($user), 403);

        $conversations = $this->conversationListFor($request);

        $conversation->load(['userOne.profileModules', 'userTwo.profileModules', 'messages.user', 'commissionRequest.artist', 'commissionRequest.requester']);
        $conversation->markReadFor($user);

        return view('conversations.index', [
            'conversations' => $conversations,
            'activeConversation' => $conversation,
            'otherParty' => $conversation->otherPartyFor($user),
            'commissionRequest' => $conversation->commissionRequest,
            'isCommissionConversation' => $conversation->kind === 'commission' && $conversation->commissionRequest,
            'isArtistView' => $conversation->commissionRequest?->artist_id === $user->id,
            'searchQuery' => (string) $request->query('q', ''),
        ]);
    }

    public function attachment(Request $request, Conversation $conversation, ConversationMessage $message, int $index): StreamedResponse
    {
        $user = $request->user();
        abort_unless($conversation->includesUser($user), 403);
        abort_unless($message->conversation_id === $conversation->id, 404);

        $attachment = collect($message->attachments ?? [])->values()->get($index);
        abort_unless(is_array($attachment) && !empty($attachment['path']), 404);
        abort_unless(Storage::disk('public')->exists($attachment['path']), 404);

        return Storage::disk('public')->response(
            $attachment['path'],
            $attachment['name'] ?? basename($attachment['path'])
        );
    }

    public function destroy(Request $request, Conversation $conversation): RedirectResponse
    {
        $user = $request->user();
        abort_unless($conversation->includesUser($user), 403);

        $conversation->load(['messages', 'commissionRequest.messages', 'commissionRequest.workspaceItems']);

        foreach ($conversation->messages as $message) {
            foreach (($message->attachments ?? []) as $attachment) {
                if (!empty($attachment['path'])) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }
        }

        if ($conversation->commissionRequest) {
            foreach ($conversation->commissionRequest->messages as $message) {
                foreach (($message->attachments ?? []) as $attachment) {
                    if (!empty($attachment['path'])) {
                        Storage::disk('public')->delete($attachment['path']);
                    }
                }
            }

            foreach ($conversation->commissionRequest->workspaceItems as $item) {
                if ($item->file_path) {
                    Storage::disk('public')->delete($item->file_path);
                }
            }

            foreach (($conversation->commissionRequest->reference_images ?? []) as $referenceImage) {
                if (!empty($referenceImage['path'])) {
                    Storage::disk('public')->delete($referenceImage['path']);
                }
            }

            $conversation->commissionRequest->delete();
        }

        $conversation->delete();

        return redirect()->route('conversations.index')->with('status', 'Chat deleted.');
    }

    public function storeMessage(Request $request, Conversation $conversation): RedirectResponse
    {
        $user = $request->user();
        abort_unless($conversation->includesUser($user), 403);

        $payload = $request->validate([
            'message' => ['nullable', 'string', 'max:4000', 'required_without:attachments'],
            'attachments' => ['nullable', 'array', 'max:8'],
            'attachments.*' => ['image', 'max:10240'],
        ]);

        $attachments = collect($request->file('attachments', []))
            ->filter()
            ->map(function ($file) {
                $path = $file->store('conversation-attachments', 'public');

                return [
                    'path' => $path,
                    'url' => Storage::disk('public')->url($path),
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ];
            })
            ->values()
            ->all();

        ConversationMessage::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'kind' => 'message',
            'message' => trim((string) ($payload['message'] ?? '')),
            'attachments' => $attachments,
        ]);

        $conversation->markReadFor($user);
        $conversation->touch();

        if ($conversation->commissionRequest) {
            $conversation->commissionRequest->markReadFor($user);
            $conversation->commissionRequest->touch();
        }

        return back()->with('status', 'Message sent.');
    }
}
