@extends('layouts.public')

@section('title', 'Sedang Dalam Pemeliharaan - Desa ' . env('VILLAGE_NAME', 'Damai'))

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center text-center px-4">
    <div class="text-primary-700 text-6xl mb-6">ðŸ› ï¸ </div>
    <h1 class="text-3xl sm:text-4xl font-black text-surface-900 mb-4">Sedang Dalam Pemeliharaan</h1>
    <p class="text-surface-600 max-w-md mx-auto mb-8">
        Mohon maaf, Website Resmi Desa {{ env('VILLAGE_NAME', 'Damai') }} saat ini sedang dalam proses pemeliharaan rutin atau peningkatan sistem. Kami akan segera kembali beroperasi.
    </p>
    <x-button href="#" onclick="window.location.reload(); return false;" variant="primary" size="md">
        Coba Muat Ulang
    </x-button>
</div>
@endsection
