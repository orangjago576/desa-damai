<?php

use App\Http\Controllers\PointOfInterestController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\DB;
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

Route::get('/_diag', function () {
    $out = [];
    try {
        $out[] = 'driver pdo_pgsql: ' . (extension_loaded('pdo_pgsql') ? 'ada' : 'TIDAK ADA');
        $out[] = 'select 1: ' . json_encode(DB::select('select 1 as ok'));
        $out[] = 'activities: ' . DB::table('activities')->count();
        
        // Test simulasi persis PublicController::home()
        $activities = \App\Models\Activity::with(['category', 'author'])
            ->published()
            ->latest('activity_date')
            ->take(3)
            ->get();
        $out[] = 'eloquent activities: ' . $activities->count();

        $activeVillageHead = \App\Models\VillageHead::active()->first();
        $out[] = 'activeVillageHead: ' . ($activeVillageHead ? $activeVillageHead->name : 'none');

        // Test render view
        $view = view('welcome', compact('activities', 'activeVillageHead'))->render();
        $out[] = 'view render: OK (length ' . strlen($view) . ')';

        return response(implode("\n", $out), 200)->header('Content-Type', 'text/plain');
    } catch (\Throwable $e) {
        $out[] = 'ERROR CLASS: ' . get_class($e);
        $out[] = 'MESSAGE: ' . $e->getMessage();
        $out[] = 'FILE: ' . $e->getFile() . ':' . $e->getLine();
        return response(implode("\n", $out), 500)->header('Content-Type', 'text/plain');
    }
});