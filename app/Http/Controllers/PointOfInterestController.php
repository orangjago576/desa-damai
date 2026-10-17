<?php

namespace App\Http\Controllers;

use App\Models\PointOfInterest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PointOfInterestController extends Controller
{
    public const CATEGORY_LABELS = [
        'kantor_desa'       => 'Kantor Desa',
        'rumah_kepala_desa' => 'Rumah Kepala Desa',
        'rumah_kadus'       => 'Rumah Kepala Dusun',
        'fasilitas_umum'    => 'Fasilitas Umum',
        'lainnya'           => 'Lainnya',
    ];

    public function index(Request $request): View
    {
        $selectedCategory = $request->query('category');

        $query = PointOfInterest::query();

        if ($selectedCategory && array_key_exists($selectedCategory, self::CATEGORY_LABELS)) {
            $query->where('category', $selectedCategory);
        }

        $points = $query->orderBy('name', 'asc')->get();

        // Data JSON untuk Leaflet JS
        $mapPoints = $points->map(function ($poi) {
            return [
                'id'          => $poi->id,
                'name'        => $poi->name,
                'category'    => $poi->category,
                'category_label' => self::CATEGORY_LABELS[$poi->category] ?? $poi->category,
                'address'     => $poi->address ?? 'Alamat tidak dicantumkan',
                'hamlet'      => $poi->hamlet,
                'latitude'    => (float) $poi->latitude,
                'longitude'   => (float) $poi->longitude,
                'description' => $poi->description,
                'photo_url'   => $poi->photo_path ? asset('storage/' . $poi->photo_path) : null,
                'gmaps_url'   => "https://www.google.com/maps?q={$poi->latitude},{$poi->longitude}",
            ];
        });

        return view('public.peta', [
            'points'           => $points,
            'mapPointsJson'    => json_encode($mapPoints),
            'categories'       => self::CATEGORY_LABELS,
            'selectedCategory' => $selectedCategory,
        ]);
    }
}
