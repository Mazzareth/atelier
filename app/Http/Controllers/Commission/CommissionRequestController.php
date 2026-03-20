<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Models\CommissionMessage;
use App\Models\CommissionRequest;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CommissionRequestController extends Controller
{
    public function create(Request $request, string $username): View
    {
        $artist = User::whereRaw('LOWER(username) = ?', [strtolower($username)])->firstOrFail();

        abort_unless($artist->isArtist(), 404);

        return view('commission.create', [
            'artist' => $artist,
        ]);
    }

    public function store(Request $request, string $username): RedirectResponse
    {
        $artist = User::whereRaw('LOWER(username) = ?', [strtolower($username)])->firstOrFail();
        abort_unless($artist->isArtist(), 404);

        $payload = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'details' => ['required', 'string', 'max:5000'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'references' => ['nullable', 'array', 'max:6'],
            'references.*' => ['image', 'max:10240'],
        ]);

        $referenceImages = collect($request->file('references', []))
            ->filter()
            ->map(function ($file) {
                $path = $file->store('commission-references', 'public');

                return [
                    'path' => $path,
                    'url' => Storage::disk('public')->url($path),
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ];
            })
            ->values()
            ->all();

        $conversation = Conversation::create([
            'user_one_id' => $artist->id,
            'user_two_id' => $request->user()->id,
            'kind' => 'commission',
            'title' => $payload['title'],
            'user_one_last_read_at' => null,
            'user_two_last_read_at' => now(),
        ]);

        $commissionRequest = CommissionRequest::create([
            'artist_id' => $artist->id,
            'requester_id' => $request->user()->id,
            'conversation_id' => $conversation->id,
            'title' => $payload['title'],
            'details' => $payload['details'],
            'budget' => $payload['budget'] ?? null,
            'reference_images' => $referenceImages,
            'status' => 'pending',
        ]);

        CommissionMessage::create([
            'commission_request_id' => $commissionRequest->id,
            'user_id' => $request->user()->id,
            'kind' => 'system',
            'message' => 'Commission request created.',
        ]);

        ConversationMessage::create([
            'conversation_id' => $conversation->id,
            'user_id' => $request->user()->id,
            'kind' => 'system',
            'message' => 'Commission request created.',
        ]);

        $openingMessage = $this->buildOpeningRequestMessage($payload, !empty($referenceImages));

        CommissionMessage::create([
            'commission_request_id' => $commissionRequest->id,
            'user_id' => $request->user()->id,
            'kind' => 'message',
            'message' => $openingMessage,
            'attachments' => $referenceImages,
        ]);

        ConversationMessage::create([
            'conversation_id' => $conversation->id,
            'user_id' => $request->user()->id,
            'kind' => 'message',
            'message' => $openingMessage,
            'attachments' => $referenceImages,
        ]);

        foreach ($referenceImages as $index => $referenceImage) {
            $commissionRequest->workspaceItems()->create([
                'type' => 'image',
                'title' => $referenceImage['name'] ?? ('Reference ' . ($index + 1)),
                'file_path' => $referenceImage['path'] ?? null,
                'x' => 56 + (($index % 3) * 44),
                'y' => 56 + (int) floor($index / 3) * 44,
                'width' => 280,
                'height' => 280,
                'z_index' => $index + 1,
                'meta' => [
                    'source' => 'commission_reference',
                    'reference_index' => $index,
                ],
            ]);
        }

        $commissionRequest->forceFill([
            'requester_last_read_at' => now(),
            'artist_last_read_at' => null,
        ])->save();

        return redirect()->route('commission.show', $commissionRequest)
            ->with('status', 'Commission request sent. You can keep chatting with the artist here.');
    }

    public function show(Request $request, CommissionRequest $commissionRequest): RedirectResponse
    {
        $user = $request->user();
        abort_unless(
            $commissionRequest->artist_id === $user->id || $commissionRequest->requester_id === $user->id,
            403
        );

        return redirect()->route('conversations.show', $commissionRequest->conversation);
    }

    protected function buildOpeningRequestMessage(array $payload, bool $hasReferences = false): string
    {
        $lines = [
            '## Commission request',
            '',
            '**Title:** ' . $payload['title'],
        ];

        if (!empty($payload['budget'])) {
            $lines[] = '**Budget:** $' . number_format((float) $payload['budget'], 2);
        }

        $lines[] = '';
        $lines[] = $payload['details'];

        if ($hasReferences) {
            $lines[] = '';
            $lines[] = '_Attached reference images included below._';
        }

        return implode("\n", $lines);
    }
}
