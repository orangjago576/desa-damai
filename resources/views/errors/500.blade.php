@extends('layouts.public')

@section('title', 'Terjadi Kesalahan Server - Desa ' . env('VILLAGE_NAME', 'Damai'))

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-16">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-rose-50 text-rose-600 border border-rose-200 shadow-xs">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-rose-700 bg-rose-100 px-3 py-1 rounded-full">
                Error 500
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-surface-900 tracking-tight">
                Terjadi Kesalahan Server
            </h1>
            <p class="text-sm text-surface-600 leading-relaxed">
                Mohon maaf, sistem server kami sedang mengalami gangguan sementara. Silakan coba muat ulang halaman beberapa saat lagi.
            </p>
        </div>

        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
            <x-button href="javascript:location.reload()" variant="primary" size="md">
                Muat Ulang Halaman
            </x-button>
            <x-button href="{{ url('/') }}" variant="outline" size="md">
                Kembali ke Beranda
            </x-button>
        </div>
    </div>
</div>
@endsection
