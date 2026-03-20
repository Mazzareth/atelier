<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Models\CommissionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommissionNotificationController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();

        $requests = CommissionRequest::with(['artist', 'requester', 'messages' => fn ($query) => $query->latest('id')->limit(1)])
            ->where(function ($query) use ($user) {
                $query->where('artist_id', $user->id)
                    ->orWhere('requester_id', $user->id);
            })
            ->latest('updated_at')
            ->limit(8)
            ->get();

        $items = $requests->map(function (CommissionRequest $commissionRequest) use ($user) {
            $otherParty = $commissionRequest->artist_id === $user->id
                ? $commissionRequest->requester
                : $commissionRequest->artist;

            $latestMessage = $commissionRequest->messages->first();
            $unread = $commissionRequest->unreadCountFor($user);

            return [
                'id' => $commissionRequest->id,
                'title' => $commissionRequest->title,
                'status' => $commissionRequest->status,
                'otherParty' => [
                    'name' => $otherParty?->name,
                    'username' => $otherParty?->username,
                ],
                'url' => route('commission.show', $commissionRequest),
                'updatedHuman' => $commissionRequest->updated_at->diffForHumans(),
                'latestMessage' => $latestMessage ? [
                    'kind' => $latestMessage->kind,
                    'message' => $latestMessage->message,
                    'createdHuman' => $latestMessage->created_at->diffForHumans(),
                ] : null,
                'unread' => $unread,
            ];
        })->values();

        return response()->json([
            'totalUnread' => $items->sum('unread'),
            'items' => $items,
        ]);
    }

    public function thread(Request $request, CommissionRequest $commissionRequest): JsonResponse
    {
        $user = $request->user();
        abort_unless($commissionRequest->artist_id === $user->id || $commissionRequest->requester_id === $user->id, 403);

        $commissionRequest->load(['messages.user']);
        $commissionRequest->markReadFor($user);

        return response()->json([
            'messages' => $commissionRequest->messages
                ->sortBy('id')
                ->values()
                ->map(fn ($message) => [
                    'id' => $message->id,
                    'kind' => $message->kind,
                    'message' => $message->message,
                    'createdHuman' => $message->created_at->format('M j, Y g:i A'),
                    'userName' => $message->kind === 'system' ? 'System' : ($message->user?->name ?? 'User'),
                    'isMine' => $message->user_id === $user->id,
                ]),
            'status' => $commissionRequest->status,
            'artistResponse' => $commissionRequest->artist_response,
            'updatedAt' => $commissionRequest->updated_at?->toIso8601String(),
        ]);
    }
}
