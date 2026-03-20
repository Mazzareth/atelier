<?php

namespace App\Http\Controllers\Atelier;

use App\Http\Controllers\Controller;
use App\Models\CommissionRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AtelierDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $artist = $request->user();

        $requests = CommissionRequest::where('artist_id', $artist->id)->get();

        $activeCount = $requests
            ->where('status', CommissionRequest::STATUS_ACCEPTED)
            ->whereIn('tracker_stage', [
                CommissionRequest::TRACKER_QUEUE,
                CommissionRequest::TRACKER_ACTIVE,
                CommissionRequest::TRACKER_DELIVERY,
            ])->count();

        $newRequestsCount = $requests->where('status', CommissionRequest::STATUS_PENDING)->count();

        $closedCount = $requests->where('status', CommissionRequest::STATUS_ACCEPTED)
            ->where('tracker_stage', CommissionRequest::TRACKER_DONE)
            ->count();

        $pendingRevenue = $requests->where('status', CommissionRequest::STATUS_ACCEPTED)
            ->whereIn('tracker_stage', [
                CommissionRequest::TRACKER_QUEUE,
                CommissionRequest::TRACKER_ACTIVE,
                CommissionRequest::TRACKER_DELIVERY,
            ])->sum(fn ($commissionRequest) => (float) ($commissionRequest->budget ?? 0));

        $lastEditedAt = optional($artist->profileModules()->latest('updated_at')->first())->updated_at;

        return view('atelier.dashboard', [
            'activeCount' => $activeCount,
            'newRequestsCount' => $newRequestsCount,
            'closedCount' => $closedCount,
            'pendingRevenue' => $pendingRevenue,
            'lastEditedAt' => $lastEditedAt,
        ]);
    }
}
