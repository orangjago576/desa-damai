<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\VillageHead;
use App\Models\Institution;
use App\Models\ActivityCategory;
use App\Models\ImportantContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicController extends Controller
{
    public function home()
    {
        $activities = Cache::remember('home_activities', 3600, function () {
            return Activity::with(['category', 'author'])
                ->published()
                ->latest('activity_date')
                ->take(3)
                ->get();
        });

        $activeVillageHead = Cache::remember('active_village_head', 3600, function () {
            return VillageHead::active()->first();
        });

        return view('welcome', compact('activities', 'activeVillageHead'));
    }

    public function activities(Request $request)
    {
        $categories = Cache::remember('activity_categories_all', 3600, function () {
            return ActivityCategory::orderBy('name')->get();
        });

        $query = Activity::with(['category', 'author'])
            ->published()
            ->latest('activity_date');

        // Filter berdasarkan kategori
        $selectedCategory = $request->query('kategori');
        if ($selectedCategory) {
            $query->whereHas('category', function ($q) use ($selectedCategory) {
                $q->where('slug', $selectedCategory);
            });
        }

        // Pencarian judul
        $search = $request->query('cari');
        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        $activities = $query->paginate(9)->appends($request->query());

        return view('public.activities.index', compact('activities', 'categories', 'selectedCategory', 'search'));
    }

    public function activityShow(string $slug)
    {
        $activity = Activity::with(['category', 'author'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // 3 kegiatan terkait dari kategori yang sama, kecuali kegiatan ini sendiri
        $relatedActivities = Activity::with(['category'])
            ->published()
            ->where('category_id', $activity->category_id)
            ->where('id', '!=', $activity->id)
            ->latest('activity_date')
            ->take(3)
            ->get();

        return view('public.activities.show', compact('activity', 'relatedActivities'));
    }

    public function institutions()
    {
        $institutions = Cache::remember('institutions_list', 3600, function () {
            return Institution::with(['members' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            }])->get();
        });

        return view('public.institutions', compact('institutions'));
    }

    public function contacts()
    {
        // CRITICAL CHECK: Pastikan hanya status publikasi aktif yang diambil.
        $contacts = Cache::remember('important_contacts', 3600, function () {
            return ImportantContact::published()->get();
        });

        return view('public.contacts', compact('contacts'));
    }

    public function sitemap()
    {
        $activities = Activity::published()->get();
        return response()->view('public.sitemap', compact('activities'))->header('Content-Type', 'text/xml');
    }
}
