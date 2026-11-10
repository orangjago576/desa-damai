<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->configureVercelRuntime();

        // Daftarkan resolver path root-relative untuk Vite sedini mungkin
        $this->app->singleton(\Illuminate\Foundation\Vite::class, function () {
            $vite = new \Illuminate\Foundation\Vite();
            $vite->createAssetPathsUsing(fn ($path) => '/' . ltrim($path, '/'));
            return $vite;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('VERCEL') || config('app.env') === 'production' || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
            URL::forceScheme('https');

            $host = request()->header('X-Forwarded-Host') ?? request()->header('Host') ?? $_SERVER['HTTP_HOST'] ?? null;
            if ($host) {
                URL::forceRootUrl('https://' . $host);
            }
            config(['app.asset_url' => null]);
        }

        // Pastikan instance Vite yang sudah aktif juga menggunakan root-relative path
        $this->app->make(\Illuminate\Foundation\Vite::class)
            ->createAssetPathsUsing(fn ($path) => '/' . ltrim($path, '/'));
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
            $storagePath.'/app/livewire-tmp',
            $storagePath.'/app/private',
            $storagePath.'/app/private/livewire-tmp',
            $storagePath.'/app/public',
            $storagePath.'/app/public/livewire-tmp',
            $storagePath.'/app/purifier',
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
            'filesystems.disks.local.root' => $storagePath.'/app/private',
            'filesystems.disks.public.root' => $storagePath.'/app/public',
            'purifier.cachePath' => $storagePath.'/app/purifier',
        ]);
    }
}
