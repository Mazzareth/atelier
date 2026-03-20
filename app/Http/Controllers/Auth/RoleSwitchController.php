<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class RoleSwitchController extends Controller
{
    /**
     * Switch the active profile for an artist.
     */
    public function switchProfile(Request $request, string $mode): RedirectResponse
    {
        $user = $request->user();

        // Validate modes
        if (! in_array($mode, ['commissioner', 'artist', 'admin'])) {
            abort(400, 'Invalid mode selected.');
        }

        // Check Permissions
        if ($mode === 'admin' && ! $user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }
        if ($mode === 'artist' && ! $user->isArtist()) {
            abort(403, 'Unauthorized.');
        }

        // Apply switch
        $user->forceFill([
            'active_profile' => $mode
        ])->save();

        // Redirect appropriately
        if ($mode === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('status', 'Switched to Admin Control. Be careful.');
        }
        if ($mode === 'artist') {
            return redirect()->route('artist.dashboard')
                ->with('status', 'Switched to Atelier View. Welcome back to work.');
        }

        return redirect()->route('dashboard')
            ->with('status', 'Switched to Personal Feed. Time to play.');
    }
}
