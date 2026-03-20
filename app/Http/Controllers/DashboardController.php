<?php

namespace App\Http\Controllers;

use App\Models\CommissionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $myRequests = CommissionRequest::with('artist', 'conversation')
            ->where('requester_id', $user->id)
            ->latest()
            ->limit(6)
            ->get();

        $followedArtistIds = DB::table('followers')
            ->where('follower_id', $user->id)
            ->pluck('user_id');

        $followedArtists = User::whereIn('id', $followedArtistIds)
            ->whereNotNull('username')
            ->with(['profileModules' => function ($query) {
                $query->whereIn('type', ['avatar_info', 'bio', 'gallery_feed', 'comm_slots']);
            }])
            ->orderByDesc('follower_count')
            ->get();

        $suggestedArtists = User::where('role', 'artist')
            ->whereNotNull('username')
            ->where('id', '!=', $user->id)
            ->when($followedArtistIds->count(), fn ($query) => $query->whereNotIn('id', $followedArtistIds))
            ->with(['profileModules' => function ($query) {
                $query->whereIn('type', ['avatar_info', 'bio', 'comm_slots']);
            }])
            ->orderByDesc('follower_count')
            ->limit(4)
            ->get();

        return view('dashboard', [
            'myRequests' => $myRequests,
            'followedArtists' => $followedArtists,
            'suggestedArtists' => $suggestedArtists,
        ]);
    }

    public function myRequests(Request $request): View
    {
        $myRequests = CommissionRequest::with('artist', 'conversation')
            ->where('requester_id', $request->user()->id)
            ->latest()
            ->get();

        return view('commission.index', [
            'myRequests' => $myRequests,
        ]);
    }
}
