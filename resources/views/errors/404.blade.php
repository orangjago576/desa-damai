@extends('layouts.public')

@section('title', 'Halaman Tidak Ditemukan - Desa ' . env('VILLAGE_NAME', 'Damai'))

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-16">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-amber-50 text-amber-600 border border-amber-200 shadow-xs">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-700 bg-amber-100 px-3 py-1 rounded-full">
                Error 404
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-surface-900 tracking-tight">
                Halaman Tidak Ditemukan
            </h1>
            <p class="text-sm text-surface-600 leading-relaxed">
                Maaf, halaman yang Anda cari tidak tersedia, telah dipindahkan, atau alamat URL yang Anda masukkan kurang tepat.
            </p>
        </div>

        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
            <x-button href="{{ url('/') }}" variant="primary" size="md">
                Kembali ke Beranda
            </x-button>
            <x-button href="{{ url('/kegiatan') }}" variant="outline" size="md">
                Lihat Kegiatan Desa
            </x-button>
        </div>
    </div>
</div>
@endsection
