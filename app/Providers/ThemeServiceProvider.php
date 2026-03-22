<?php

namespace App\Providers;

use App\Services\ThemeManifest;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register as singleton
        $this->app->singleton(ThemeManifest::class, function ($app) {
            return new ThemeManifest();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Blade directive: @theme('language.buttons.default')
        Blade::directive('theme', function (string $expression) {
            // Handle optional default value: @theme('key', 'default value')
            $parts = explode(',', $expression, 2);
            $key = trim($parts[0], "'\"");

            $default = isset($parts[1]) ? trim($parts[1], " '\"") : null;

            return "<?php echo app(\\App\\Services\\ThemeManifest::class)->get('{$key}', " .
                   ($default ? "'{$default}'" : 'null') . "); ?>";
        });

        // Register Blade directive: @button('submit')
        Blade::directive('buttonText', function (string $expression) {
            $state = trim($expression, "'\"");

            return "<?php echo app(\\App\\Services\\ThemeManifest::class)->button('{$state}'); ?>";
        });

        // Register Blade directive: @nav('home')
        Blade::directive('navLabel', function (string $expression) {
            $key = trim($expression, "'\"");

            return "<?php echo app(\\App\\Services\\ThemeManifest::class)->nav('{$key}'); ?>";
        });

        // Register Blade directive: @cardText('expand')
        Blade::directive('cardText', function (string $expression) {
            $key = trim($expression, "'\"");

            return "<?php echo app(\\App\\Services\\ThemeManifest::class)->card('{$key}'); ?>";
        });

        // Register Blade directive: @identityAware('greeting')
        // Usage: @identityAware('greeting') — resolves identity_aware.greeting_{identity}
        Blade::directive('identityAware', function (string $expression) {
            $parts = explode(',', $expression, 2);
            $keyPrefix = trim($parts[0], "'\"");

            $default = isset($parts[1]) ? trim($parts[1], " '\"") : null;

            return "<?php echo app(\\App\\Services\\ThemeManifest::class)->identityAware('{$keyPrefix}', null, " .
                   ($default ? "'{$default}'" : 'null') . "); ?>";
        });

        // Share theme data with all views
        view()->composer('*', function ($view) {
            $theme = app(ThemeManifest::class);

            $view->with([
                'theme' => $theme,
                'themeManifest' => $theme->all(),
                'themeName' => $theme->getTheme(),
                'viewerIdentity' => $theme->viewerIdentity(),
                'identityLabel' => $theme->getIdentityLabel(),
            ]);
        });
    }
}
