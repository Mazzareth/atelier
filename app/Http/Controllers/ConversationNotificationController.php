<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationNotificationController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();

        $conversations = Conversation::with(['userOne', 'userTwo', 'messages' => fn ($query) => $query->latest('id')->limit(1), 'commissionRequest'])
            ->where(function ($query) use ($user) {
                $query->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->latest('updated_at')
            ->limit(10)
            ->get();

        $items = $conversations->map(function (Conversation $conversation) use ($user) {
            $otherParty = $conversation->otherPartyFor($user);
            $latestMessage = $conversation->messages->first();
            $commissionRequest = $conversation->commissionRequest;

            return [
                'id' => $conversation->id,
                'title' => $conversation->title ?: ($commissionRequest?->title ?: ('Chat with ' . ($otherParty?->name ?? 'user'))),
                'status' => $commissionRequest?->status ?? $conversation->kind,
                'kind' => $conversation->kind,
                'otherParty' => [
                    'name' => $otherParty?->name,
                    'username' => $otherParty?->username,
                ],
                'url' => route('conversations.show', $conversation),
                'updatedHuman' => $conversation->updated_at->diffForHumans(),
                'latestMessage' => $latestMessage ? [
                    'kind' => $latestMessage->kind,
                    'message' => $latestMessage->message,
                    'createdHuman' => $latestMessage->created_at->diffForHumans(),
                ] : null,
                'unread' => $conversation->unreadCountFor($user),
            ];
        })->values();

        return response()->json([
            'totalUnread' => $items->sum('unread'),
            'items' => $items,
        ]);
    }
}
