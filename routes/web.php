<?php

use App\Http\Controllers\PointOfInterestController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/kegiatan', [PublicController::class, 'activities'])->name('activities.index');
Route::get('/kegiatan/{slug}', [PublicController::class, 'activityShow'])->name('activities.show');
Route::get('/lembaga', [PublicController::class, 'institutions'])->name('institutions.index');
Route::get('/kontak', [PublicController::class, 'contacts'])->name('contacts.index');

Route::get('/peta', [PointOfInterestController::class, 'index'])->name('peta.index');

// Route utilitas satu kali klik untuk menjalankan migrasi database di Vercel/Cloud
Route::get('/init-db', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate --force');
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

        \Illuminate\Support\Facades\Artisan::call('db:seed --force');
        $seedOutput = \Illuminate\Support\Facades\Artisan::output();

        return response("<pre style='padding:20px;background:#0f172a;color:#22c55e;'>DATABASE BERHASIL DIINISIALISASI!\n\n=== MIGRASI ===\n{$migrateOutput}\n=== SEEDER ===\n{$seedOutput}</pre>");
    } catch (\Throwable $e) {
        return response("<pre style='padding:20px;background:#0f172a;color:#ef4444;'>ERROR SAAT KONEKSI KE DATABASE:\n\n" . $e->getMessage() . "\n\n" . $e->getTraceAsString() . "</pre>", 500);
    }
});

Route::get('/sitemap.xml', [PublicController::class, 'sitemap'])->name('sitemap');

Route::get('/robots.txt', function () {
    return response("User-agent: *\nDisallow:\nSitemap: " . url('/sitemap.xml'), 200)
        ->header('Content-Type', 'text/plain');
});
