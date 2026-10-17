@extends('layouts.public')

@section('title', 'Kegiatan Desa - Desa ' . env('VILLAGE_NAME', 'Damai'))
@section('meta_description', 'Daftar kegiatan, agenda, dan program kerja terbaru Desa ' . env('VILLAGE_NAME', 'Damai') . '. Informasi resmi kegiatan desa.')

@section('content')
<div class="space-y-8 pb-12">
    {{-- Header Halaman --}}
    <section class="bg-gradient-to-b from-primary-900 via-primary-800 to-primary-950 text-white pt-8 pb-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <x-breadcrumb :items="[['label' => 'Kegiatan Desa', 'url' => '']]" />

            <div class="mt-4 space-y-2">
                <x-badge variant="accent" size="md">Informasi Kegiatan</x-badge>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight">
                    Kegiatan & Berita Desa
                </h1>
                <p class="text-xs sm:text-sm md:text-base text-primary-100 max-w-2xl">
                    Informasi terkini mengenai agenda, program kerja, dan kegiatan pembangunan Desa {{ env('VILLAGE_NAME', 'Damai') }}.
                </p>
            </div>
        </div>
    </section>

    {{-- Filter & Pencarian --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('activities.index') }}" class="flex flex-col sm:flex-row gap-3" x-data>
            {{-- Pencarian Judul --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="text"
                    name="cari"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari kegiatan berdasarkan judul..."
                    class="w-full pl-10 pr-4 py-2.5 text-sm border border-surface-300 rounded-lg bg-white text-surface-800 placeholder-surface-400 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition-colors"
                >
            </div>

            {{-- Filter Kategori --}}
            <div class="flex gap-2">
                <select
                    name="kategori"
                    class="w-full sm:w-auto min-w-[160px] px-3 py-2.5 text-sm border border-surface-300 rounded-lg bg-white text-surface-800 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition-colors"
                >
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}" {{ ($selectedCategory ?? '') === $category->slug ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <x-button type="submit" variant="primary" size="md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span class="hidden sm:inline">Cari</span>
                </x-button>
            </div>
        </form>

        {{-- Indikator Filter Aktif --}}
        @if($search || $selectedCategory)
            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-surface-600">
                <span>Filter aktif:</span>
                @if($search)
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-50 text-primary-800 rounded-full border border-primary-200/80">
                        Pencarian: "{{ $search }}"
                        <a href="{{ route('activities.index', array_filter(['kategori' => $selectedCategory])) }}" class="hover:text-primary-900" aria-label="Hapus filter pencarian">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </span>
                @endif
                @if($selectedCategory)
                    @php $catName = $categories->firstWhere('slug', $selectedCategory)?->name ?? $selectedCategory; @endphp
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-50 text-primary-800 rounded-full border border-primary-200/80">
                        Kategori: {{ $catName }}
                        <a href="{{ route('activities.index', array_filter(['cari' => $search])) }}" class="hover:text-primary-900" aria-label="Hapus filter kategori">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </span>
                @endif
                <a href="{{ route('activities.index') }}" class="text-primary-700 hover:text-primary-800 font-semibold hover:underline">
                    Hapus Semua Filter
                </a>
            </div>
        @endif
    </div>

    {{-- Daftar Kegiatan --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($activities->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($activities as $activity)
                <a href="{{ route('activities.show', $activity->slug) }}" class="group block">
                    <x-card hover="true" padding="p-0" class="overflow-hidden h-full flex flex-col">
                        {{-- Gambar Sampul --}}
                        <div class="relative aspect-[16/10] bg-surface-100 overflow-hidden">
                            @if($activity->cover_image)
                                <img
                                    src="{{ asset('storage/' . $activity->cover_image) }}"
                                    alt="{{ $activity->title }}"
                                    width="400"
                                    height="250"
                                    loading="lazy"
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-surface-100">
                                    <svg class="w-12 h-12 text-surface-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Konten Kartu --}}
                        <div class="p-4 sm:p-5 flex flex-col flex-1 space-y-3">
                            <div class="flex justify-between items-center">
                                <x-badge variant="primary" size="sm">{{ $activity->category->name ?? 'Umum' }}</x-badge>
                                <span class="text-[11px] text-surface-400">{{ $activity->activity_date->translatedFormat('d M Y') }}</span>
                            </div>

                            <h3 class="text-sm sm:text-base font-bold text-surface-900 group-hover:text-primary-700 transition-colors line-clamp-2 leading-snug">
                                {{ $activity->title }}
                            </h3>

                            <p class="text-xs text-surface-600 line-clamp-2 flex-1">
                                {{ $activity->excerpt ?? Str::limit(strip_tags($activity->content), 120) }}
                            </p>

                            <div class="pt-2 border-t border-surface-100 flex items-center justify-between text-xs">
                                @if($activity->location)
                                    <span class="text-surface-500 flex items-center gap-1 truncate max-w-[60%]">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $activity->location }}
                                    </span>
                                @else
                                    <span class="text-surface-500">{{ $activity->author->name ?? 'Admin' }}</span>
                                @endif
                                <span class="font-semibold text-primary-700 group-hover:underline">Baca &rarr;</span>
                            </div>
                        </div>
                    </x-card>
                </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <x-pagination :paginator="$activities" />
        @else
            {{-- Empty State --}}
            @if($search || $selectedCategory)
                <x-empty-state
                    title="Kegiatan tidak ditemukan"
                    description="Tidak ada kegiatan yang cocok dengan filter atau pencarian Anda. Coba ubah kata kunci atau hapus filter."
                    icon="search"
                >
                    <x-slot:action>
                        <x-button href="{{ route('activities.index') }}" variant="outline" size="md">
                            Hapus Filter & Tampilkan Semua
                        </x-button>
                    </x-slot:action>
                </x-empty-state>
            @else
                <x-empty-state
                    title="Belum ada kegiatan diterbitkan"
                    description="Belum ada kegiatan desa yang dipublikasikan saat ini. Kegiatan baru akan ditampilkan di sini setelah diterbitkan oleh admin desa."
                    icon="newspaper"
                />
            @endif
        @endif
    </div>
</div>
@endsection
