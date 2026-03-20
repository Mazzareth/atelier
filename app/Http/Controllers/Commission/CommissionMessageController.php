<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Models\CommissionMessage;
use App\Models\CommissionRequest;
use App\Models\ConversationMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CommissionMessageController extends Controller
{
    public function attachment(Request $request, CommissionRequest $commissionRequest, CommissionMessage $message, int $index): StreamedResponse
    {
        $user = $request->user();
        abort_unless(
            $commissionRequest->artist_id === $user->id || $commissionRequest->requester_id === $user->id,
            403
        );
        abort_unless($message->commission_request_id === $commissionRequest->id, 404);

        $attachment = collect($message->attachments ?? [])->values()->get($index);
        abort_unless(is_array($attachment) && !empty($attachment['path']), 404);
        abort_unless(Storage::disk('public')->exists($attachment['path']), 404);

        return Storage::disk('public')->response(
            $attachment['path'],
            $attachment['name'] ?? basename($attachment['path'])
        );
    }

    public function store(Request $request, CommissionRequest $commissionRequest): RedirectResponse
    {
        $user = $request->user();
        abort_unless(
            $commissionRequest->artist_id === $user->id || $commissionRequest->requester_id === $user->id,
            403
        );

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

        CommissionMessage::create([
            'commission_request_id' => $commissionRequest->id,
            'user_id' => $user->id,
            'kind' => 'message',
            'message' => trim((string) ($payload['message'] ?? '')),
            'attachments' => $attachments,
        ]);

        if ($commissionRequest->conversation_id) {
            ConversationMessage::create([
                'conversation_id' => $commissionRequest->conversation_id,
                'user_id' => $user->id,
                'kind' => 'message',
                'message' => trim((string) ($payload['message'] ?? '')),
                'attachments' => $attachments,
            ]);
            $commissionRequest->conversation?->markReadFor($user);
            $commissionRequest->conversation?->touch();
        }

        $commissionRequest->markReadFor($user);
        $commissionRequest->touch();

        return back()->with('status', 'Message sent.');
    }
}
