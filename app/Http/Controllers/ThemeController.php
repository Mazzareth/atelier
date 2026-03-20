<?php

namespace App\Http\Controllers;

use App\Services\ThemeManifest;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Switch to a different theme
     */
    public function switch(Request $request, string $theme)
    {
        // Store in session
        session(['theme' => $theme]);

        // If user is logged in, save to their profile
        if (auth()->check()) {
            auth()->user()->update(['theme' => $theme]);
        }

        // Clear theme manifest cache
        ThemeManifest::clearCache($theme);

        // Redirect back or to intended page
        return redirect()->back()->with('status', "Theme switched to: {$theme}");
    }

    /**
     * Preview a theme (uses query param)
     */
    public function preview(Request $request, string $theme)
    {
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
