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

Route::get('/sitemap.xml', [PublicController::class, 'sitemap'])->name('sitemap');

Route::get('/robots.txt', function () {
    return response("User-agent: *\nDisallow:\nSitemap: " . url('/sitemap.xml'), 200)
        ->header('Content-Type', 'text/plain');
});
