@extends('layouts.public')

@section('title', 'Peta Lokasi Penting - Desa ' . env('VILLAGE_NAME', 'Damai'))

@section('meta_description', 'Peta lokasi penting dan fasilitas umum Desa ' . env('VILLAGE_NAME', 'Damai') . '. Kantor desa, rumah kepala dusun, dan lokasi penting lainnya.')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .leaflet-popup-content-wrapper {
        border-radius: 0.75rem;
        padding: 0.25rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .poi-popup-img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 0.5rem 0.5rem 0 0;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">
    <x-breadcrumb :items="[['label' => 'Peta Lokasi Penting', 'url' => '']]" />

    <x-section-heading 
        badge="Fasilitas & Wilayah"
        title="Peta Lokasi Penting Desa"
        subtitle="Temukan posisi kantor desa, rumah perangkat desa, dan fasilitas umum penting dengan peta interaktif."
    />

    <!-- Filter Kategori di Atas Peta -->
    <div x-data="{
        activeCategory: 'semua',
        allPoints: {{ $mapPointsJson }},
        filteredPoints: [],
        map: null,
        markersLayer: null,
        isLoaded: false,

        init() {
            this.filteredPoints = this.allPoints;
        },

        lazyLoadMap() {
            if (this.isLoaded) return;
            this.isLoaded = true;

            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
            script.crossOrigin = '';
            script.onload = () => this.buildLeafletMap();
            document.head.appendChild(script);
        },

        buildLeafletMap() {
            const mapContainer = this.$refs.mapContainer;
            if (!mapContainer || this.allPoints.length === 0) return;

            // Pusat peta default ke titik pertama atau desa
            const firstLat = this.allPoints[0]?.latitude || -7.550762;
            const firstLng = this.allPoints[0]?.longitude || 110.818130;

            this.map = L.map(mapContainer).setView([firstLat, firstLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: 'Â© <a href=\'https://www.openstreetmap.org/\'>OpenStreetMap</a>'
            }).addTo(this.map);

            this.markersLayer = L.layerGroup().addTo(this.map);
            this.renderMarkers();
        },

        filterCategory(cat) {
            this.activeCategory = cat;
            if (cat === 'semua') {
                this.filteredPoints = this.allPoints;
            } else {
                this.filteredPoints = this.allPoints.filter(p => p.category === cat);
            }
            if (this.map && this.markersLayer) {
                this.renderMarkers();
            }
        },

        renderMarkers() {
            this.markersLayer.clearLayers();
            const bounds = [];

            this.filteredPoints.forEach(point => {
                const markerPos = [point.latitude, point.longitude];
                bounds.push(markerPos);

                const marker = L.marker(markerPos);

                let photoHtml = point.photo_url 
                    ? `<img src='${point.photo_url}' class='poi-popup-img mb-2' alt='${point.name}' loading='lazy'>` 
                    : '';

                const popupContent = `
                    <div class='p-2 max-w-xs text-surface-800'>
                        ${photoHtml}
                        <span class='inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-primary-100 text-primary-800 mb-1'>
                            ${point.category_label}
                        </span>
                        <h4 class='text-sm font-bold text-surface-900 leading-tight mb-1'>${point.name}</h4>
                        <p class='text-xs text-surface-600 mb-2'>${point.address}</p>
                        <a href='${point.gmaps_url}' target='_blank' rel='noopener noreferrer' 
                           class='inline-flex items-center justify-center gap-1.5 w-full px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary-700 text-white hover:bg-primary-800 transition-colors shadow-xs'>
                            <span>&#x1F4CD; Buka di Google Maps</span>
                        </a>
                    </div>
                `;

                marker.bindPopup(popupContent);
                this.markersLayer.addLayer(marker);
            });

            if (bounds.length > 0 && this.map) {
                this.map.fitBounds(bounds, { padding: [40, 40], maxZoom: 16 });
            }
        }
    }" 
    x-init="init()"
    class="space-y-6">

        <!-- Tombol Filter Kategori -->
        <div class="flex flex-wrap items-center gap-2 pb-2 border-b border-surface-200">
            <button type="button" 
                    @click="filterCategory('semua')"
                    :class="activeCategory === 'semua' ? 'bg-primary-700 text-white shadow-xs' : 'bg-white text-surface-700 hover:bg-surface-100 border border-surface-200'"
                    class="px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all">
                Semua Tempat ({{ count($points) }})
            </button>
            @foreach($categories as $key => $label)
                @php
                    $countKey = $points->where('category', $key)->count();
                @endphp
                <button type="button" 
                        @click="filterCategory('{{ $key }}')"
                        :class="activeCategory === '{{ $key }}' ? 'bg-primary-700 text-white shadow-xs' : 'bg-white text-surface-700 hover:bg-surface-100 border border-surface-200'"
                        class="px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all">
                    {{ $label }} ({{ $countKey }})
                </button>
            @endforeach
        </div>

        <!-- Container Peta Interaktif (Lazy Loaded via x-intersect / scroll) -->
        <div class="bg-white rounded-xl border border-surface-200 shadow-sm overflow-hidden p-2">
            <div x-intersect.once="lazyLoadMap()" class="relative">
                <div x-ref="mapContainer" 
                     class="w-full h-[400px] sm:h-[500px] rounded-lg bg-surface-100 z-0 flex items-center justify-center text-surface-500">
                    <template x-if="!isLoaded">
                        <div class="flex flex-col items-center gap-2 p-6 text-center">
                            <svg class="w-8 h-8 text-primary-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-xs font-semibold text-surface-600">Memuat Peta Interaktif Desa...</span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Daftar Titik Lokasi (Fallback SSR untuk Jaringan Lambat / Tanpa JS) -->
        <div class="pt-6 space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-surface-900">
                    Daftar Lokasi Penting Desa
                </h3>
                <span class="text-xs text-surface-500">
                    Fallback untuk koneksi lambat
                </span>
            </div>

            @if($points->isEmpty())
                <x-empty-state 
                    title="Belum ada lokasi penting yang ditambahkan"
                    description="Data titik lokasi kantor desa dan fasilitas umum akan segera diperbarui oleh admin."
                    icon="map"
                />
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($points as $poi)
                        <div x-show="activeCategory === 'semua' || activeCategory === '{{ $poi->category }}'"
                             class="bg-white rounded-xl border border-surface-200 p-4 shadow-xs hover:border-primary-300 transition-all flex flex-col justify-between">
                            <div class="space-y-2">
                                @if($poi->photo_path)
                                    <img src="{{ asset('storage/' . $poi->photo_path) }}" 
                                         alt="{{ $poi->name }}" 
                                         class="w-full h-36 object-cover rounded-lg mb-2" 
                                         loading="lazy">
                                @endif
                                <div class="flex items-center justify-between">
                                    <x-badge variant="primary" size="sm">
                                        {{ \App\Http\Controllers\PointOfInterestController::CATEGORY_LABELS[$poi->category] ?? $poi->category }}
                                    </x-badge>
                                    @if($poi->hamlet)
                                        <span class="text-[11px] text-surface-500 font-medium">{{ $poi->hamlet }}</span>
                                    @endif
                                </div>
                                <h4 class="text-sm font-bold text-surface-900 leading-snug">
                                    {{ $poi->name }}
                                </h4>
                                @if($poi->address)
                                    <p class="text-xs text-surface-600 leading-normal">
                                        &#x1F4CD; {{ $poi->address }}
                                    </p>
                                @endif
                                @if($poi->description)
                                    <p class="text-xs text-surface-500 line-clamp-2">
                                        {{ $poi->description }}
                                    </p>
                                @endif
                            </div>

                            <div class="pt-4 mt-3 border-t border-surface-100 flex items-center justify-between gap-2">
                                <span class="text-[11px] font-mono text-surface-400">
                                    {{ number_format($poi->latitude, 4) }}, {{ number_format($poi->longitude, 4) }}
                                </span>
                                <x-button 
                                    href="https://www.google.com/maps?q={{ $poi->latitude }},{{ $poi->longitude }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    variant="primary" 
                                    size="sm">
                                    Buka di Google Maps
                                </x-button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

