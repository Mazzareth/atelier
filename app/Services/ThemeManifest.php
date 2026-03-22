<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class ThemeManifest
{
    private const ALLOWED_THEMES = ['default', 'rubber', 'femboy', 'dominant'];

    protected array $manifest = [];
    protected string $theme;
    protected string $manifestPath;

    public function __construct(?string $theme = null)
    {
        $this->theme = $theme ?? $this->getActiveTheme();
        $this->manifestPath = $this->getManifestPath($this->theme);
        $this->loadManifest();
    }

    /**
     * Get the active theme from session or user preference
     */
    protected function getActiveTheme(): string
    {
        // Check user's saved theme preference
        if (auth()->check() && auth()->user()->theme) {
            return $this->normalizeTheme(auth()->user()->theme);
        }

        // Check session
        if (session()->has('theme')) {
            return $this->normalizeTheme((string) session('theme'));
        }

        // Default to 'default' theme
        return 'default';
    }

    protected function normalizeTheme(?string $theme): string
    {
        return in_array($theme, self::ALLOWED_THEMES, true) ? $theme : 'default';
    }

    /**
     * Get the manifest path for a theme
     */
    protected function getManifestPath(string $theme): string
    {
        $basePath = resource_path('themes');
        
        // If theme has a manifest, use it
        $themePath = "{$basePath}/{$theme}/manifest.json";
        if (File::exists($themePath)) {
            return $themePath;
        }

        // Fall back to default theme
        return "{$basePath}/default/manifest.json";
    }

    /**
     * Load the manifest into memory
     */
    protected function loadManifest(): void
    {
        $cacheKey = "theme_manifest_{$this->theme}";

        $this->manifest = Cache::remember($cacheKey, 3600, function () {
            if (!File::exists($this->manifestPath)) {
                return $this->getDefaultManifest();
            }

            $content = File::get($this->manifestPath);
            return json_decode($content, true) ?? $this->getDefaultManifest();
        });
    }

    /**
     * Get the default manifest structure
     */
    protected function getDefaultManifest(): array
    {
        return [
            'name' => 'Default',
            'personality' => 'Clean and professional',
            'language' => [
                'buttons' => [
                    'default' => 'Submit',
                    'hover' => 'Click',
                    'loading' => 'Loading...',
                    'disabled' => 'Disabled',
                ],
                'nav' => [
                    'home' => 'Home',
                    'profile' => 'Profile',
                    'settings' => 'Settings',
                    'dashboard' => 'Dashboard',
                    'logout' => 'Logout',
                ],
                'cards' => [
                    'collapse' => 'Show Less',
                    'expand' => 'Show More',
                    'close' => 'Close',
                ],
                'messages' => [
                    'welcome' => 'Welcome',
                    'success' => 'Success',
                    'error' => 'Error',
                    'loading' => 'Loading...',
                ],
            ],
            'layout' => [
                'navStyle' => 'standard',
                'cardGap' => 'normal',
                'contentDensity' => 'normal',
                'maxWidth' => '1180px',
            ],
            'behaviors' => [
                'hover' => 'lift',
                'click' => 'press',
                'transition' => 'smooth',
            ],
        ];
    }

    /**
     * Get a value from the manifest using dot notation
     * 
     * @param string $key Dot-notation key (e.g., 'language.buttons.default')
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->manifest;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Get the entire manifest
     */
    public function all(): array
    {
        return $this->manifest;
    }

    /**
     * Get just the language section
     */
    public function language(string $path = null, mixed $default = null): mixed
    {
        if ($path === null) {
            return $this->manifest['language'] ?? [];
        }

        return $this->get("language.{$path}", $default);
    }

    /**
     * Get language for a button (handles states)
     */
    public function button(string $state = 'default'): string
    {
        return $this->get("language.buttons.{$state}", 'Submit');
    }

    /**
     * Get nav label
     */
    public function nav(string $key): string
    {
        return $this->get("language.nav.{$key}", ucfirst($key));
    }

    /**
     * Get card label
     */
    public function card(string $key): string
    {
        return $this->get("language.cards.{$key}", ucfirst($key));
    }

    /**
     * Get layout setting
     */
    public function layout(string $key, mixed $default = null): mixed
    {
        return $this->get("layout.{$key}", $default);
    }

    /**
     * Get behavior setting
     */
    public function behavior(string $key, mixed $default = null): mixed
    {
        return $this->get("behaviors.{$key}", $default);
    }

    /**
     * Check if theme has a specific feature
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Get the current theme name
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * Check if current theme is a specific one
     */
    public function is(string $theme): bool
    {
        return $this->theme === $theme;
    }

    /**
     * Check if this theme requires identity selection
     */
    public function requiresIdentity(): bool
    {
        return (bool) $this->get('requires_identity', false);
    }

    /**
     * Get identity-aware language string.
     * Falls back to the base key if no identity-aware variant exists.
     *
     * @param string $key Dot-notation key (e.g., 'identity_aware.greeting_male')
     * @param string|null $identity The viewer's identity (male, female, dickgirl, other)
     * @param string|null $default Fallback if nothing found
     * @return string
     */
    public function identityAware(string $keyPrefix, ?string $identity = null, ?string $default = null): string
    {
        if ($identity === null) {
            $identity = $this->resolveViewerIdentity();
        }

        if (!$identity) {
            return $default ?? '';
        }

        // Try identity-specific key: e.g. identity_aware.greeting_male
        $identityKey = "{$keyPrefix}_{$identity}";
        $result = $this->get("identity_aware.{$identityKey}");

        if ($result) {
            return $result;
        }

        // Fall back to other
        $result = $this->get("identity_aware.{$keyPrefix}_other");
        if ($result) {
            return $result;
        }

        return $default ?? '';
    }

    /**
     * Get the treatment label for the current viewer identity.
     */
    public function getIdentityLabel(?string $identity = null): string
    {
        if ($identity === null) {
            $identity = $this->resolveViewerIdentity();
        }

        $labels = $this->get("identity.{$identity}.label", 'Guest');
        return $labels;
    }

    /**
     * Get the identity treatment type for the current viewer identity.
     */
    public function getIdentityTreatment(?string $identity = null): string
    {
        if ($identity === null) {
            $identity = $this->resolveViewerIdentity();
        }

        return $this->get("identity.{$identity}.treatment", 'welcomed');
    }

    /**
     * Get the identity pronoun for the current viewer identity.
     */
    public function getIdentityPronoun(?string $identity = null): string
    {
        if ($identity === null) {
            $identity = $this->resolveViewerIdentity();
        }

        return $this->get("identity.{$identity}.pronoun", 'they');
    }

    /**
     * Resolve the viewer's identity from session or authenticated user.
     */
    protected function resolveViewerIdentity(): ?string
    {
        // Check session first
        if (session()->has('viewer_identity')) {
            return session('viewer_identity');
        }

        // Check authenticated user
        if (auth()->check() && auth()->user()->viewer_identity) {
            return auth()->user()->viewer_identity;
        }

        return null;
    }

    /**
     * Get current viewer identity (public)
     */
    public function viewerIdentity(): ?string
    {
        return $this->resolveViewerIdentity();
    }

    /**
     * Clear manifest cache (call after theme change)
     */
    public static function clearCache(string $theme = null): void
    {
        if ($theme) {
            $theme = in_array($theme, self::ALLOWED_THEMES, true) ? $theme : 'default';
            Cache::forget("theme_manifest_{$theme}");
        } else {
            // Clear all theme caches
            $themes = self::ALLOWED_THEMES;
            foreach ($themes as $t) {
                Cache::forget("theme_manifest_{$t}");
            }
        }
    }
}
