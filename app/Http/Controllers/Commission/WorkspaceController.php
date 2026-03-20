<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Models\CommissionMessage;
use App\Models\CommissionRequest;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\WorkspaceConnection;
use App\Models\WorkspaceItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    public function index(Request $request, ?CommissionRequest $commissionRequest = null): View
    {
        $artist = $request->user();

        $commissions = CommissionRequest::with(['requester', 'workspaceItems'])
            ->where('artist_id', $artist->id)
            ->orderByRaw("CASE WHEN tracker_stage = 'active' THEN 0 WHEN tracker_stage = 'queue' THEN 1 WHEN tracker_stage = 'delivery' THEN 2 WHEN tracker_stage = 'done' THEN 3 ELSE 4 END")
            ->latest('tracker_stage_updated_at')
            ->latest()
            ->get();

        $activeCommission = $commissionRequest;
        if (! $activeCommission && $commissions->count()) {
            $activeCommission = $commissions->first();
        }

        if ($activeCommission) {
            abort_unless($activeCommission->artist_id === $artist->id, 403);
            $activeCommission->load(['requester', 'conversation', 'workspaceItems', 'workspaceConnections']);
        }

        return view('atelier.workspace.index', [
            'commissions' => $commissions,
            'activeCommission' => $activeCommission,
            'trackerStages' => CommissionRequest::trackerStageOptions(),
        ]);
    }

    public function storeManualCommission(Request $request): RedirectResponse
    {
        $artist = $request->user();

        $payload = $request->validate([
            'client_name' => ['required', 'string', 'max:160'],
            'client_contact' => ['nullable', 'string', 'max:190'],
            'title' => ['required', 'string', 'max:160'],
            'details' => ['required', 'string', 'max:5000'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'tracker_stage' => ['required', 'string', 'in:' . implode(',', CommissionRequest::trackerStageOptions())],
        ]);

        $conversation = Conversation::create([
            'user_one_id' => $artist->id,
            'user_two_id' => $artist->id,
            'kind' => 'commission',
            'title' => $payload['title'],
            'user_one_last_read_at' => now(),
            'user_two_last_read_at' => now(),
            'meta' => [
                'manual_client_name' => $payload['client_name'],
                'manual_client_contact' => $payload['client_contact'] ?? null,
                'is_manual_commission' => true,
            ],
        ]);

        $commissionRequest = CommissionRequest::create([
            'artist_id' => $artist->id,
            'requester_id' => $artist->id,
            'conversation_id' => $conversation->id,
            'title' => $payload['title'],
            'details' => $payload['details'],
            'budget' => $payload['budget'] ?? null,
            'status' => CommissionRequest::STATUS_ACCEPTED,
            'tracker_stage' => $payload['tracker_stage'],
            'tracker_stage_updated_at' => now(),
            'responded_at' => now(),
            'client_name' => $payload['client_name'],
            'client_contact' => $payload['client_contact'] ?? null,
            'is_manual' => true,
        ]);

        CommissionMessage::create([
            'commission_request_id' => $commissionRequest->id,
            'user_id' => $artist->id,
            'kind' => 'system',
            'message' => 'Manual commission created in Workspace.',
        ]);

        ConversationMessage::create([
            'conversation_id' => $conversation->id,
            'user_id' => $artist->id,
            'kind' => 'system',
            'message' => 'Manual commission created in Workspace.',
        ]);

        return redirect()->route('artist.workspace.show', $commissionRequest)->with('status', 'Manual commission added to Workspace.');
    }

    public function updateStage(Request $request, CommissionRequest $commissionRequest): RedirectResponse
    {
        abort_unless($commissionRequest->artist_id === $request->user()->id, 403);

        $payload = $request->validate([
            'tracker_stage' => ['required', 'string', 'in:' . implode(',', CommissionRequest::trackerStageOptions())],
        ]);

        $commissionRequest->update([
            'status' => CommissionRequest::STATUS_ACCEPTED,
            'tracker_stage' => $payload['tracker_stage'],
            'tracker_stage_updated_at' => now(),
        ]);

        return redirect()->route('artist.workspace.show', $commissionRequest)->with('status', 'Workspace stage updated.');
    }

    public function uploadAsset(Request $request, CommissionRequest $commissionRequest): JsonResponse
    {
        abort_unless($commissionRequest->artist_id === $request->user()->id, 403);

        $payload = $request->validate([
            'asset' => ['required', 'image', 'max:10240'],
        ]);

        $path = $request->file('asset')->store('workspace-assets', 'public');

        $item = $commissionRequest->workspaceItems()->create([
            'type' => 'image',
            'title' => $request->file('asset')->getClientOriginalName(),
            'file_path' => $path,
            'x' => 48,
            'y' => 48,
            'width' => 320,
            'height' => 240,
            'z_index' => ($commissionRequest->workspaceItems()->max('z_index') ?? 0) + 1,
        ]);

        return response()->json([
            'status' => 'created',
            'item' => $this->workspaceItemPayload($item),
        ]);
    }

    public function storeNote(Request $request, CommissionRequest $commissionRequest): JsonResponse
    {
        abort_unless($commissionRequest->artist_id === $request->user()->id, 403);

        $payload = $request->validate([
            'title' => ['nullable', 'string', 'max:120'],
            'content' => ['nullable', 'string', 'max:12000'],
        ]);

        $item = $commissionRequest->workspaceItems()->create([
            'type' => 'note',
            'title' => $payload['title'] ?: 'Reference note',
            'content' => $payload['content'] ?? '',
            'x' => 72,
            'y' => 72,
            'width' => 320,
            'height' => 220,
            'z_index' => ($commissionRequest->workspaceItems()->max('z_index') ?? 0) + 1,
        ]);

        return response()->json([
            'status' => 'created',
            'item' => $this->workspaceItemPayload($item),
        ]);
    }

    public function storeGroup(Request $request, CommissionRequest $commissionRequest): JsonResponse
    {
        abort_unless($commissionRequest->artist_id === $request->user()->id, 403);

        $payload = $request->validate([
            'title' => ['nullable', 'string', 'max:120'],
            'background' => ['nullable', 'string', 'max:32'],
            'x' => ['nullable', 'integer'],
            'y' => ['nullable', 'integer'],
        ]);

        $item = $commissionRequest->workspaceItems()->create([
            'type' => 'group',
            'title' => $payload['title'] ?: 'New group',
            'background' => $payload['background'] ?: 'rgba(43,220,108,0.08)',
            'x' => $payload['x'] ?? 120,
            'y' => $payload['y'] ?? 120,
            'width' => 420,
            'height' => 280,
            'z_index' => 1,
        ]);

        return response()->json([
            'status' => 'created',
            'item' => $this->workspaceItemPayload($item),
        ]);
    }

    public function storeConnection(Request $request, CommissionRequest $commissionRequest): JsonResponse
    {
        abort_unless($commissionRequest->artist_id === $request->user()->id, 403);

        $payload = $request->validate([
            'from_workspace_item_id' => ['required', 'integer'],
            'to_workspace_item_id' => ['required', 'integer', 'different:from_workspace_item_id'],
        ]);

        $from = $commissionRequest->workspaceItems()->findOrFail($payload['from_workspace_item_id']);
        $to = $commissionRequest->workspaceItems()->findOrFail($payload['to_workspace_item_id']);

        $existing = $commissionRequest->workspaceConnections()
            ->where(function ($query) use ($from, $to) {
                $query->where('from_workspace_item_id', $from->id)->where('to_workspace_item_id', $to->id);
            })
            ->orWhere(function ($query) use ($from, $to) {
                $query->where('from_workspace_item_id', $to->id)->where('to_workspace_item_id', $from->id);
            })
            ->first();

        if ($existing) {
            return response()->json(['status' => 'exists', 'connection' => ['id' => $existing->id]], 200);
        }

        $connection = $commissionRequest->workspaceConnections()->create([
            'from_workspace_item_id' => $from->id,
            'to_workspace_item_id' => $to->id,
        ]);

        return response()->json([
            'status' => 'created',
            'connection' => [
                'id' => $connection->id,
                'from_workspace_item_id' => $connection->from_workspace_item_id,
                'to_workspace_item_id' => $connection->to_workspace_item_id,
            ],
        ]);
    }

    public function updateItem(Request $request, CommissionRequest $commissionRequest, WorkspaceItem $workspaceItem): JsonResponse
    {
        abort_unless($commissionRequest->artist_id === $request->user()->id, 403);
        abort_unless($workspaceItem->commission_request_id === $commissionRequest->id, 404);

        $payload = $request->validate([
            'title' => ['nullable', 'string', 'max:120'],
            'content' => ['nullable', 'string', 'max:12000'],
            'x' => ['nullable', 'integer'],
            'y' => ['nullable', 'integer'],
            'width' => ['nullable', 'integer', 'min:120', 'max:2400'],
            'height' => ['nullable', 'integer', 'min:80', 'max:2400'],
            'z_index' => ['nullable', 'integer', 'min:1'],
        ]);

        $workspaceItem->update($payload);

        return response()->json(['status' => 'updated']);
    }

    public function deleteItem(Request $request, CommissionRequest $commissionRequest, WorkspaceItem $workspaceItem): JsonResponse
    {
        abort_unless($commissionRequest->artist_id === $request->user()->id, 403);
        abort_unless($workspaceItem->commission_request_id === $commissionRequest->id, 404);

        if ($workspaceItem->file_path) {
            Storage::disk('public')->delete($workspaceItem->file_path);
        }

        $workspaceItem->delete();

        return response()->json(['status' => 'deleted']);
    }

    public function deleteConnection(Request $request, CommissionRequest $commissionRequest, WorkspaceConnection $workspaceConnection): JsonResponse
    {
        abort_unless($commissionRequest->artist_id === $request->user()->id, 403);
        abort_unless($workspaceConnection->commission_request_id === $commissionRequest->id, 404);

        $workspaceConnection->delete();

        return response()->json(['status' => 'deleted']);
    }

    public function asset(CommissionRequest $commissionRequest, WorkspaceItem $workspaceItem)
    {
        abort_unless(auth()->check() && $commissionRequest->artist_id === auth()->id(), 403);
        abort_unless($workspaceItem->commission_request_id === $commissionRequest->id, 404);
        abort_unless($workspaceItem->file_path, 404);

        $path = Storage::disk('public')->path($workspaceItem->file_path);
        abort_unless(is_file($path), 404);

        return response()->file($path);
    }

    protected function workspaceItemPayload(WorkspaceItem $item): array
    {
        return [
            'id' => $item->id,
            'type' => $item->type,
            'title' => $item->title,
            'content' => $item->content,
            'background' => $item->background,
            'file_url' => $item->file_path ? route('artist.workspace.items.asset', [$item->commission_request_id, $item]) : null,
            'x' => $item->x,
            'y' => $item->y,
            'width' => $item->width,
            'height' => $item->height,
            'z_index' => $item->z_index,
        ];
    }
}
