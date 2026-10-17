@extends('layouts.public')

@section('title', 'Website Resmi Desa ' . env('VILLAGE_NAME', 'Damai'))

@section('content')
<div class="space-y-12 sm:space-y-16 pb-12">
    <!-- Hero Banner Pengenalan -->
    <section class="bg-gradient-to-b from-primary-900 via-primary-800 to-primary-950 text-white pt-10 pb-16 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="max-w-7xl mx-auto relative z-10">
            <x-breadcrumb :items="[['label' => 'Selamat Datang', 'url' => '']]" />

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center mt-6">
                <div class="lg:col-span-8 space-y-4">
                    <x-badge variant="accent" size="md">
                        Portal Informasi Resmi Desa
                    </x-badge>
                    <h1 class="text-2xl sm:text-4xl md:text-5xl font-black tracking-tight leading-tight">
                        Selamat Datang di Website Resmi Desa {{ env('VILLAGE_NAME', 'Damai') }}
                    </h1>
                    <p class="text-xs sm:text-base md:text-lg text-primary-100 max-w-2xl leading-relaxed">
                        Mewujudkan transparansi digital, kemudahan akses informasi publik, serta direktori lengkap kegiatan dan fasilitas desa.
                    </p>
                    <div class="pt-2 flex flex-wrap items-center gap-3">
                        <x-button href="{{ url('/kegiatan') }}" variant="accent" size="md">
                            Lihat Kegiatan Desa
                        </x-button>
                        <x-button href="{{ url('/peta') }}" variant="outline" size="md" class="!bg-white/10 !text-white !border-white/20 hover:!bg-white/20">
                            Jelajahi Peta Lokasi
                        </x-button>
                    </div>
                </div>

                <div class="lg:col-span-4">
                    <x-card class="!bg-white/95 backdrop-blur-md text-surface-900 border-none shadow-xl">
                        <div class="flex items-center gap-3 mb-3">
                            @if(isset($activeVillageHead) && $activeVillageHead->photo_path)
                                <img src="{{ asset('storage/' . $activeVillageHead->photo_path) }}" alt="Foto Kepala Desa" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-800 flex items-center justify-center text-lg font-bold">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                            @endif
                            <div>
                                <h3 class="text-sm font-bold">{{ $activeVillageHead->name ?? 'Kepala Desa' }}</h3>
                                <p class="text-xs text-surface-500">Kepala Desa Aktif</p>
                            </div>
                        </div>
                        <p class="text-xs text-surface-600 italic">
                            "{{ $activeVillageHead->welcome_speech ?? 'Selamat datang di portal pelayanan digital desa kami. Kami siap melayani warga dengan transparan dan akuntabel.' }}"
                        </p>
                    </x-card>
                </div>
            </div>
        </div>
    </section>

    <!-- Showcase Komponen & Preview Layout -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <!-- Section Heading & Card Grid -->
        <section>
            <x-section-heading 
                badge="Informasi Terbaru"
                title="Kegiatan & Berita Desa"
                subtitle="Dapatkan informasi terkini mengenai agenda dan program kerja pembangunan desa."
                actionUrl="{{ url('/kegiatan') }}"
                actionText="Lihat Semua Kegiatan"
            />

            @if(isset($activities) && $activities->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($activities as $activity)
                    <a href="{{ route('activities.show', $activity->slug) }}" class="group block h-full">
                        <x-card hover="true" padding="p-0" class="overflow-hidden h-full flex flex-col">
                            {{-- Gambar Sampul dengan Fixed Aspect Ratio --}}
                            <div class="relative aspect-[16/10] bg-surface-100 overflow-hidden">
                                @if($activity->cover_image)
                                    <img 
                                        src="{{ asset('storage/' . $activity->cover_image) }}" 
                                        alt="{{ $activity->title }}" 
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                        loading="lazy"
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
                                    {{ $activity->excerpt ?? Str::limit(strip_tags($activity->content), 100) }}
                                </p>
                                <div class="pt-3 border-t border-surface-100 flex items-center justify-between text-xs">
                                    <span class="text-surface-500">Oleh: {{ $activity->author->name ?? 'Admin' }}</span>
                                    <span class="font-semibold text-primary-700 group-hover:underline flex items-center gap-1">
                                        Baca Selengkapnya &rarr;
                                    </span>
                                </div>
                            </div>
                        </x-card>
                    </a>
                    @endforeach
                </div>
            @else
                <x-empty-state 
                    title="Belum ada pengumuman baru"
                    description="Pengumuman resmi dari Pemerintah Desa akan ditampilkan di bagian ini."
                    icon="newspaper"
                />
            @endif
        </section>
    </div>
</div>
@endsection
