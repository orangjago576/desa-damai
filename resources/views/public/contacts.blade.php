@extends('layouts.public')

@section('title', 'Kontak Penting - Desa ' . env('VILLAGE_NAME', 'Damai'))
@section('meta_description', 'Daftar kontak penting perangkat dan pejabat Desa ' . env('VILLAGE_NAME', 'Damai') . '. Hubungi melalui WhatsApp atau telepon.')

@section('content')
<div class="space-y-8 pb-12">
    {{-- Header Halaman --}}
    <section class="bg-gradient-to-b from-primary-900 via-primary-800 to-primary-950 text-white pt-8 pb-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <x-breadcrumb :items="[['label' => 'Kontak Penting', 'url' => '']]" />

            <div class="mt-4 space-y-2">
                <x-badge variant="accent" size="md">Layanan Informasi</x-badge>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight">
                    Kontak Penting Desa
                </h1>
                <p class="text-sm sm:text-base text-primary-200 max-w-2xl">
                    Direktori nomor telepon dan WhatsApp perangkat desa yang dapat dihubungi. Hanya kontak yang telah disetujui untuk dipublikasikan yang ditampilkan.
                </p>
            </div>
        </div>
    </section>

    {{-- Daftar Kontak --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($contacts->isEmpty())
            <x-empty-state
                title="Belum ada kontak yang dipublikasikan"
                description="Data kontak penting perangkat desa akan ditampilkan setelah mendapatkan persetujuan publikasi dari admin."
                icon="folder-open"
            />
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($contacts as $contact)
                    <x-card hover padding="p-5">
                        <div class="flex items-start gap-4">
                            {{-- Avatar --}}
                            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>

                            {{-- Info Kontak --}}
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-bold text-surface-900 truncate">
                                    {{ $contact->name }}
                                </h3>
                                <p class="text-xs text-primary-700 font-medium">
                                    {{ $contact->position }}
                                </p>
                                @if($contact->work_area)
                                    <p class="text-[11px] text-surface-500 mt-0.5">
                                        {{ $contact->work_area }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Nomor & Tombol Aksi --}}
                        <div class="mt-4 pt-3 border-t border-surface-100 flex items-center justify-between gap-2">
                            <span class="text-sm font-mono text-surface-700">
                                {{ $contact->phone_number }}
                            </span>

                            <div class="flex items-center gap-2">
                                {{-- Tombol Telepon --}}
                                <a href="tel:{{ $contact->phone_number }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-surface-100 text-surface-600 hover:bg-surface-200 transition-colors"
                                   title="Telepon {{ $contact->name }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </a>

                                {{-- Tombol WhatsApp --}}
                                @php
                                    $waNumber = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $contact->phone_number));
                                @endphp
                                <a href="https://wa.me/{{ $waNumber }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 text-green-700 hover:bg-green-200 transition-colors"
                                   title="WhatsApp {{ $contact->name }}">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
