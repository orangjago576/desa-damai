<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->configureVercelRuntime();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }

    private function configureVercelRuntime(): void
    {
        $isVercel = isset($_ENV['VERCEL']) || getenv('VERCEL') !== false || isset($_SERVER['VERCEL']);

        if (! $isVercel) {
            return;
        }

        $storagePath = '/tmp/storage';
        $viewPath = '/tmp/storage/framework/views';
        $cachePath = '/tmp/bootstrap-cache';

        foreach ([
            $storagePath,
            $storagePath.'/app',
            $storagePath.'/app/private',
            $storagePath.'/app/public',
            $storagePath.'/framework',
            $storagePath.'/framework/cache',
            $storagePath.'/framework/cache/data',
            $storagePath.'/framework/sessions',
            $storagePath.'/framework/testing',
            $storagePath.'/framework/views',
            $storagePath.'/logs',
            $cachePath,
        ] as $directory) {
            if (! is_dir($directory)) {
                @mkdir($directory, 0775, true);
            }
        }

        $this->app->useStoragePath($storagePath);

        config([
            'view.compiled' => $viewPath,
            'cache.stores.file.path' => $storagePath.'/framework/cache/data',
            'session.files' => $storagePath.'/framework/sessions',
        ]);
    }
}
