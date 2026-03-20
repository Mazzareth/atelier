<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Models\CommissionMessage;
use App\Models\CommissionRequest;
use App\Models\ConversationMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArtistRequestInboxController extends Controller
{
    protected function trackerGroupsForArtist(int $artistId)
    {
        return CommissionRequest::with('requester')
            ->where('artist_id', $artistId)
            ->where('status', CommissionRequest::STATUS_ACCEPTED)
            ->whereNotNull('tracker_stage')
            ->latest('tracker_stage_updated_at')
            ->latest()
            ->get()
            ->groupBy('tracker_stage');
    }

    public function index(Request $request): View
    {
        $artist = $request->user();

        $requests = CommissionRequest::with('requester')
            ->where('artist_id', $artist->id)
            ->latest()
            ->get();

        $trackerGroups = $this->trackerGroupsForArtist($artist->id);

        return view('atelier.requests.index', [
            'requests' => $requests,
            'trackerGroups' => $trackerGroups,
        ]);
    }

    public function tracker(Request $request): View
    {
        $artist = $request->user();

        $acceptedRequests = CommissionRequest::with('requester')
            ->where('artist_id', $artist->id)
            ->where('status', CommissionRequest::STATUS_ACCEPTED)
            ->latest('tracker_stage_updated_at')
            ->latest()
            ->get();

        $trackerGroups = $acceptedRequests
            ->whereNotNull('tracker_stage')
            ->groupBy('tracker_stage');

        $untrackedRequests = $acceptedRequests->whereNull('tracker_stage')->values();

        return view('atelier.requests.tracker', [
            'acceptedRequests' => $acceptedRequests,
            'trackerGroups' => $trackerGroups,
            'untrackedRequests' => $untrackedRequests,
        ]);
    }

    public function respond(Request $request, CommissionRequest $commissionRequest): RedirectResponse
    {
        abort_unless($commissionRequest->artist_id === $request->user()->id, 403);

        $payload = $request->validate([
            'action' => ['required', 'string', 'in:' . implode(',', [
                CommissionRequest::STATUS_ACCEPTED,
                CommissionRequest::STATUS_DECLINED,
                CommissionRequest::STATUS_NEEDS_INFO,
            ])],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $commissionRequest->update([
            'status' => $payload['action'],
            'tracker_stage' => $payload['action'] === CommissionRequest::STATUS_ACCEPTED ? CommissionRequest::TRACKER_QUEUE : null,
            'artist_response' => $payload['reason'] ?: null,
            'responded_at' => now(),
            'tracker_stage_updated_at' => $payload['action'] === CommissionRequest::STATUS_ACCEPTED ? now() : null,
        ]);

        $systemMessage = match ($payload['action']) {
            'accepted' => 'Status: accepted',
            'declined' => 'Status: declined',
            'needs_info' => 'Status: needs info',
        };

        if (!empty($payload['reason'])) {
            $systemMessage .= ' — ' . Str::limit($payload['reason'], 1800);
        }

        CommissionMessage::create([
            'commission_request_id' => $commissionRequest->id,
            'user_id' => $request->user()->id,
            'kind' => 'system',
            'message' => $systemMessage,
        ]);

        if ($commissionRequest->conversation_id) {
            ConversationMessage::create([
                'conversation_id' => $commissionRequest->conversation_id,
                'user_id' => $request->user()->id,
                'kind' => 'system',
                'message' => $systemMessage,
            ]);
            $commissionRequest->conversation?->markReadFor($request->user());
            $commissionRequest->conversation?->touch();
        }

        $commissionRequest->markReadFor($request->user());
        $commissionRequest->touch();

        return back()->with('status', 'Request updated.');
    }

    public function undo(Request $request, CommissionRequest $commissionRequest): RedirectResponse
    {
        abort_unless($commissionRequest->artist_id === $request->user()->id, 403);

        $commissionRequest->update([
            'status' => CommissionRequest::STATUS_PENDING,
            'tracker_stage' => null,
            'artist_response' => null,
            'responded_at' => null,
            'tracker_stage_updated_at' => null,
        ]);

        CommissionMessage::create([
            'commission_request_id' => $commissionRequest->id,
            'user_id' => $request->user()->id,
            'kind' => 'system',
            'message' => 'Status: pending',
        ]);

        if ($commissionRequest->conversation_id) {
            ConversationMessage::create([
                'conversation_id' => $commissionRequest->conversation_id,
                'user_id' => $request->user()->id,
                'kind' => 'system',
                'message' => 'Status: pending',
            ]);
            $commissionRequest->conversation?->markReadFor($request->user());
            $commissionRequest->conversation?->touch();
        }

        $commissionRequest->markReadFor($request->user());
        $commissionRequest->touch();

        return back()->with('status', 'Request reset to pending.');
    }

    public function updateTrackerStage(Request $request, CommissionRequest $commissionRequest): RedirectResponse
    {
        abort_unless($commissionRequest->artist_id === $request->user()->id, 403);
        abort_unless($commissionRequest->status === CommissionRequest::STATUS_ACCEPTED, 422);

        $payload = $request->validate([
            'tracker_stage' => ['required', 'string', 'in:' . implode(',', CommissionRequest::trackerStageOptions())],
        ]);

        $commissionRequest->update([
            'tracker_stage' => $payload['tracker_stage'],
            'tracker_stage_updated_at' => now(),
        ]);

        $stageLabel = str_replace('_', ' ', $payload['tracker_stage']);
        $systemMessage = 'Update: ' . $stageLabel;

        CommissionMessage::create([
            'commission_request_id' => $commissionRequest->id,
            'user_id' => $request->user()->id,
            'kind' => 'system',
            'message' => $systemMessage,
        ]);

        if ($commissionRequest->conversation_id) {
            ConversationMessage::create([
                'conversation_id' => $commissionRequest->conversation_id,
                'user_id' => $request->user()->id,
                'kind' => 'system',
                'message' => $systemMessage,
            ]);
            $commissionRequest->conversation?->markReadFor($request->user());
            $commissionRequest->conversation?->touch();
        }

        $commissionRequest->markReadFor($request->user());
        $commissionRequest->touch();

        return back()->with('status', 'Commission tracker updated.');
    }
}
