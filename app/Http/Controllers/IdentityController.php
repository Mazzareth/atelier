<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IdentityController extends Controller
{
    /**
     * Show the identity selector for dickgirl-themed users.
     * This fires when a user with a dickgirl theme has no identity set.
     */
    public function show(Request $request)
    {
        $theme = $request->session()->get('theme') ?? (auth()->check() ? auth()->user()->theme : null);

        // Only relevant for dickgirl themes
        $dickgirlThemes = ['dickgirl-dom', 'dickgirl-mommy', 'dickgirl'];
        if (!in_array($theme, $dickgirlThemes)) {
            return redirect('/');
        }

        // Get current identity if already set
        $currentIdentity = $request->session()->get('viewer_identity')
            ?? (auth()->check() ? auth()->user()->viewer_identity : null);

        return view('identity.select', [
            'theme' => $theme,
            'currentIdentity' => $currentIdentity,
        ]);
    }

    /**
     * Store the viewer's identity preference.
     */
    public function store(Request $request)
    {
        $theme = $request->session()->get('theme') ?? (auth()->check() ? auth()->user()->theme : null);

        $dickgirlThemes = ['dickgirl-dom', 'dickgirl-mommy', 'dickgirl'];
        if (!in_array($theme, $dickgirlThemes)) {
            return redirect('/');
        }

        $payload = $request->validate([
            'identity' => ['required', 'string', 'in:male,female,dickgirl,other'],
            'other_preference' => ['nullable', 'string', 'max:200'],
            'redirect_to' => ['nullable', 'string'],
        ]);

        // Store in session
        $request->session()->put('viewer_identity', $payload['identity']);
        if ($payload['identity'] === 'other' && !empty($payload['other_preference'])) {
            $request->session()->put('viewer_identity_other', $payload['other_preference']);
        }

        // Store on user if logged in
        if (auth()->check()) {
            auth()->user()->update(['viewer_identity' => $payload['identity']]);
        }

        // Redirect back to where they were, or browse
        $redirect = $payload['redirect_to'] ?? ($request->session()->pull('identity_redirect_back') ?? '/browse');
        return redirect($redirect)->with('identity_set', true);
    }

    /**
     * Clear identity (for switching)
     */
    public function destroy(Request $request)
    {
        $request->session()->forget(['viewer_identity', 'viewer_identity_other']);
        if (auth()->check()) {
            auth()->user()->update(['viewer_identity' => null]);
        }

        return redirect()->route('identity.select');
    }
}
