<?php

namespace App\Http\Controllers;

use App\Services\ThemeManifest;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    private const ALLOWED_THEMES = ['default', 'rubber', 'femboy', 'dominant'];

    /**
     * Switch to a different theme
     */
    public function switch(Request $request, string $theme)
    {
        if (!in_array($theme, self::ALLOWED_THEMES, true)) {
            $theme = 'default';
        }

        // Store in session
        session(['theme' => $theme]);
        // Clear any existing identity when theme changes
        session()->forget('viewer_identity');

        // If user is logged in, save to their profile
        if (auth()->check()) {
            auth()->user()->update(['theme' => $theme, 'viewer_identity' => null]);
        }

        // Clear theme manifest cache
        ThemeManifest::clearCache($theme);

        // Check if this theme requires identity selection
        $requiresIdentity = ['dominant'];
        $hasIdentity = session()->has('viewer_identity')
            || (auth()->check() && auth()->user()->viewer_identity);

        if (in_array($theme, $requiresIdentity) && !$hasIdentity) {
            // Redirect to identity selector
            return redirect()->route('identity.select')
                ->with('status', "Theme switched to: {$theme}");
        }

        // Redirect back or to intended page
        return redirect()->back()->with('status', "Theme switched to: {$theme}");
    }

    /**
     * Preview a theme (uses query param)
     */
    public function preview(Request $request, string $theme)
    {
        if (!in_array($theme, self::ALLOWED_THEMES, true)) {
            $theme = 'default';
        }

        session(['theme_preview' => $theme]);
        
        return redirect()->back()->with('status', "Previewing: {$theme}");
    }

    /**
     * Reset to default theme
     */
    public function reset()
    {
        session()->forget(['theme', 'theme_preview']);

        if (auth()->check()) {
            auth()->user()->update(['theme' => null]);
        }

        return redirect()->back()->with('status', 'Theme reset to default');
    }

    /**
     * List available themes
     */
    public function list()
    {
        $themes = glob(resource_path('themes/*/manifest.json'));
        
        $available = [];
        foreach ($themes as $path) {
            $themeName = basename(dirname($path));
            if (!in_array($themeName, self::ALLOWED_THEMES, true)) {
                continue;
            }
            $manifest = json_decode(file_get_contents($path), true);
            
            $available[$themeName] = [
                'name' => $manifest['name'] ?? $themeName,
                'personality' => $manifest['personality'] ?? '',
                'description' => $manifest['description'] ?? '',
            ];
        }

        return response()->json($available);
    }
}
