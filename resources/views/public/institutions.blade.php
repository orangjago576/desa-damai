@extends('layouts.public')

@section('title', 'Lembaga Desa - Desa ' . env('VILLAGE_NAME', 'Damai'))
@section('meta_description', 'Daftar lembaga desa beserta struktur kepengurusan Desa ' . env('VILLAGE_NAME', 'Damai') . '. BPD, PKK, Karang Taruna, LPM, dan lembaga desa lainnya.')

@section('content')
<div class="space-y-8 pb-12">
    {{-- Header Halaman --}}
    <section class="bg-gradient-to-b from-primary-900 via-primary-800 to-primary-950 text-white pt-8 pb-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <x-breadcrumb :items="[['label' => 'Lembaga Desa', 'url' => '']]" />

            <div class="mt-4 space-y-2">
                <x-badge variant="accent" size="md">Organisasi Desa</x-badge>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight">
                    Lembaga Desa
                </h1>
                <p class="text-sm sm:text-base text-primary-200 max-w-2xl">
                    Struktur kepengurusan lembaga-lembaga resmi yang berperan aktif dalam pembangunan dan pemberdayaan masyarakat desa.
                </p>
            </div>
        </div>
    </section>

    {{-- Daftar Lembaga --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($institutions->isEmpty())
            <x-empty-state
                title="Belum ada data lembaga desa"
                description="Data lembaga desa beserta struktur kepengurusannya akan segera diperbarui oleh admin."
                icon="folder-open"
            />
        @else
            <div class="space-y-8">
                @foreach($institutions as $institution)
                    <x-card padding="p-0">
                        {{-- Header Lembaga --}}
                        <div class="bg-gradient-to-r from-primary-50 to-primary-100/50 border-b border-surface-200 p-5 sm:p-6 rounded-t-xl">
                            <div class="flex items-start gap-4">
                                @if($institution->logo_path)
                                    <img src="{{ asset('storage/' . $institution->logo_path) }}"
                                         alt="Logo {{ $institution->name }}"
                                         class="w-12 h-12 sm:w-14 sm:h-14 object-contain rounded-lg bg-white border border-surface-200 p-1.5 flex-shrink-0"
                                         loading="lazy">
                                @else
                                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg bg-primary-700 text-white flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <h3 class="text-lg sm:text-xl font-bold text-surface-900 leading-snug">
                                        {{ $institution->name }}
                                    </h3>
                                    @if($institution->description)
                                        <p class="mt-1 text-xs sm:text-sm text-surface-600 leading-relaxed line-clamp-3">
                                            {{ $institution->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Daftar Anggota / Pengurus --}}
                        <div class="p-5 sm:p-6">
                            @if($institution->members->isEmpty())
                                <p class="text-sm text-surface-500 text-center py-4">
                                    Data pengurus belum tersedia.
                                </p>
                            @else
                                <h4 class="text-xs font-bold uppercase tracking-wider text-surface-500 mb-4">
                                    Struktur Kepengurusan
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($institution->members as $member)
                                        <div class="flex items-center gap-3 p-3 rounded-lg border border-surface-100 hover:border-primary-200 hover:bg-primary-50/30 transition-all">
                                            @if($member->photo_path)
                                                <img src="{{ asset('storage/' . $member->photo_path) }}"
                                                     alt="{{ $member->name }}"
                                                     class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover border-2 border-surface-200 flex-shrink-0"
                                                     loading="lazy">
                                            @else
                                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-surface-200 text-surface-500 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-surface-900 truncate">
                                                    {{ $member->name }}
                                                </p>
                                                <p class="text-xs text-primary-700 font-medium truncate">
                                                    {{ $member->position }}
                                                </p>
                                                @if($member->period)
                                                    <p class="text-[11px] text-surface-400">
                                                        Periode: {{ $member->period }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </x-card>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
