<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FollowController extends Controller
{
    /**
     * Follow/unfollow an artist.
     */
    public function toggle(Request $request, $username)
    {
        $artist = User::whereRaw('LOWER(username) = ?', [strtolower($username)])->firstOrFail();
        $me = $request->user();

        if ($me->id === $artist->id) {
            return response()->json(['error' => 'You cannot follow yourself.'], 422);
        }

        $isFollowing = DB::table('followers')
            ->where('user_id', $artist->id)
            ->where('follower_id', $me->id)
            ->exists();

        if ($isFollowing) {
            DB::table('followers')
                ->where('user_id', $artist->id)
                ->where('follower_id', $me->id)
                ->delete();
            
            $artist->decrement('follower_count');
            $status = 'unfollowed';
        } else {
            DB::table('followers')->insert([
                'user_id' => $artist->id,
                'follower_id' => $me->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $artist->increment('follower_count');
            $status = 'followed';
        }

        return response()->json([
            'status' => $status,
            'follower_count' => $artist->follower_count,
        ]);
    }
}
