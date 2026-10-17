@extends('layouts.public')

@section('title', $activity->title . ' - Kegiatan Desa ' . env('VILLAGE_NAME', 'Damai'))
@section('meta_description', $activity->excerpt ?? Str::limit(strip_tags($activity->content), 160))

{{-- Open Graph tags khusus halaman detail --}}
@section('og_image'){{ $activity->og_image ? asset('storage/' . $activity->og_image) : ($activity->cover_image ? asset('storage/' . $activity->cover_image) : asset('images/default-og.jpg')) }}@endsection

@push('styles')
{{-- Styling untuk konten rich-text dari editor admin --}}
<style>
    .prose-activity h1, .prose-activity h2, .prose-activity h3, .prose-activity h4 {
        font-weight: 700;
        color: #0f172a;
        margin-top: 1.5em;
        margin-bottom: 0.5em;
        line-height: 1.3;
    }
    .prose-activity h2 { font-size: 1.375rem; }
    .prose-activity h3 { font-size: 1.125rem; }
    .prose-activity p {
        margin-bottom: 1em;
        line-height: 1.75;
        color: #334155;
    }
    .prose-activity ul, .prose-activity ol {
        margin-bottom: 1em;
        padding-left: 1.5em;
        color: #334155;
    }
    .prose-activity ul { list-style-type: disc; }
    .prose-activity ol { list-style-type: decimal; }
    .prose-activity li { margin-bottom: 0.25em; line-height: 1.65; }
    .prose-activity img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 1.25em 0;
    }
    .prose-activity blockquote {
        border-left: 4px solid #047857;
        padding-left: 1em;
        margin: 1.25em 0;
        font-style: italic;
        color: #475569;
        background-color: #f8fafc;
        padding: 0.75em 1em;
        border-radius: 0 0.375rem 0.375rem 0;
    }
    .prose-activity a {
        color: #047857;
        text-decoration: underline;
        text-underline-offset: 2px;
    }
    .prose-activity a:hover { color: #065f46; }
    .prose-activity table {
        width: 100%;
        border-collapse: collapse;
        margin: 1em 0;
    }
    .prose-activity th, .prose-activity td {
        border: 1px solid #e2e8f0;
        padding: 0.5em 0.75em;
        text-align: left;
        font-size: 0.875rem;
    }
    .prose-activity th {
        background-color: #f1f5f9;
        font-weight: 600;
        color: #0f172a;
    }
    .prose-activity pre {
        background-color: #1e293b;
        color: #e2e8f0;
        padding: 1em;
        border-radius: 0.5rem;
        overflow-x: auto;
        font-size: 0.8125rem;
        margin: 1em 0;
    }
    .prose-activity code {
        background-color: #f1f5f9;
        padding: 0.125em 0.375em;
        border-radius: 0.25rem;
        font-size: 0.875em;
    }
    .prose-activity pre code {
        background: none;
        padding: 0;
    }
</style>
@endpush

@section('content')
@php
    $ogImage = $activity->og_image
        ? asset('storage/' . $activity->og_image)
        : ($activity->cover_image
            ? asset('storage/' . $activity->cover_image)
            : asset('images/default-og.jpg'));
    $shareUrl = urlencode(url()->current());
    $shareTitle = urlencode($activity->title);
    $shareExcerpt = urlencode($activity->excerpt ?? Str::limit(strip_tags($activity->content), 120));
@endphp

<div class="pb-12">
    {{-- Header --}}
    <section class="bg-gradient-to-b from-primary-900 via-primary-800 to-primary-950 text-white pt-8 pb-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <x-breadcrumb :items="[
                ['label' => 'Kegiatan', 'url' => route('activities.index')],
                ['label' => Str::limit($activity->title, 40)],
            ]" />
        </div>
    </section>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6">
        {{-- Artikel Utama --}}
        <article>
            <x-card padding="p-0" class="overflow-hidden">
                {{-- Gambar Sampul --}}
                @if($activity->cover_image)
                    <div class="relative aspect-[16/9] bg-surface-100">
                        <img
                            src="{{ asset('storage/' . $activity->cover_image) }}"
                            alt="{{ $activity->title }}"
                            width="896"
                            height="504"
                            loading="lazy"
                            class="w-full h-full object-cover"
                        >
                    </div>
                @endif

                <div class="p-5 sm:p-8 space-y-6">
                    {{-- Meta Informasi --}}
                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center gap-2 text-xs text-surface-500">
                            <x-badge variant="primary" size="md">{{ $activity->category->name ?? 'Umum' }}</x-badge>

                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $activity->activity_date->translatedFormat('l, d F Y') }}
                            </span>

                            @if($activity->location)
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $activity->location }}
                                </span>
                            @endif
                        </div>

                        <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-surface-900 tracking-tight leading-tight">
                            {{ $activity->title }}
                        </h1>

                        @if($activity->excerpt)
                            <p class="text-sm sm:text-base text-surface-600 leading-relaxed border-l-4 border-primary-300 pl-4 bg-primary-50/50 py-3 pr-4 rounded-r-lg">
                                {{ $activity->excerpt }}
                            </p>
                        @endif
                    </div>

                    {{-- Garis Pemisah --}}
                    <hr class="border-surface-200">

                    {{-- Konten Artikel (sanitasi XSS) --}}
                    <div class="prose-activity text-sm sm:text-base">
                        {!! clean($activity->content) !!}
                    </div>

                    {{-- Garis Pemisah --}}
                    <hr class="border-surface-200">

                    {{-- Info Penulis & Tanggal Terbit --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 text-xs text-surface-500">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-800 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                {{ strtoupper(substr($activity->author->name ?? 'A', 0, 1)) }}
                            </div>
                            <div>
                                <span class="font-semibold text-surface-700">{{ $activity->author->name ?? 'Admin Desa' }}</span>
                                @if($activity->published_at)
                                    <span class="text-surface-400">&middot; Diterbitkan {{ $activity->published_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Bagikan --}}
                    <div class="pt-4 border-t border-surface-200">
                        <p class="text-xs font-semibold text-surface-600 mb-3">Bagikan ke:</p>
                        <div class="flex flex-wrap gap-2">
                            {{-- WhatsApp --}}
                            <a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition-colors shadow-sm"
                               aria-label="Bagikan ke WhatsApp">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                WhatsApp
                            </a>

                            {{-- Facebook --}}
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition-colors shadow-sm"
                               aria-label="Bagikan ke Facebook">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Facebook
                            </a>

                            {{-- Salin Tautan --}}
                            <button
                                type="button"
                                onclick="navigator.clipboard.writeText('{{ url()->current() }}').then(() => { this.textContent = '✓ Tersalin!'; setTimeout(() => { this.innerHTML = '<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3\'/></svg> Salin Tautan'; }, 2000); })"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold border border-surface-300 bg-white text-surface-700 hover:bg-surface-50 transition-colors"
                                aria-label="Salin tautan halaman ini">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                </svg>
                                Salin Tautan
                            </button>
                        </div>
                    </div>
                </div>
            </x-card>
        </article>

        {{-- Kegiatan Terkait --}}
        @if($relatedActivities->count() > 0)
            <div class="mt-10 sm:mt-12">
                <x-section-heading
                    title="Kegiatan Terkait"
                    subtitle="Kegiatan lain dari kategori {{ $activity->category->name ?? 'yang sama' }}"
                    actionUrl="{{ route('activities.index', ['kategori' => $activity->category->slug ?? '']) }}"
                    actionText="Lihat Semua"
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($relatedActivities as $related)
                    <a href="{{ route('activities.show', $related->slug) }}" class="group block">
                        <x-card hover="true" padding="p-0" class="overflow-hidden h-full flex flex-col">
                            <div class="relative aspect-[16/10] bg-surface-100 overflow-hidden">
                                @if($related->cover_image)
                                    <img
                                        src="{{ asset('storage/' . $related->cover_image) }}"
                                        alt="{{ $related->title }}"
                                        width="400"
                                        height="250"
                                        loading="lazy"
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-surface-100">
                                        <svg class="w-10 h-10 text-surface-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4 flex flex-col flex-1 space-y-2">
                                <div class="flex justify-between items-center">
                                    <x-badge variant="primary" size="sm">{{ $related->category->name ?? 'Umum' }}</x-badge>
                                    <span class="text-[11px] text-surface-400">{{ $related->activity_date->translatedFormat('d M Y') }}</span>
                                </div>
                                <h3 class="text-sm font-bold text-surface-900 group-hover:text-primary-700 transition-colors line-clamp-2 leading-snug flex-1">
                                    {{ $related->title }}
                                </h3>
                                <span class="text-xs font-semibold text-primary-700 group-hover:underline">Baca &rarr;</span>
                            </div>
                        </x-card>
                    </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Tombol Kembali --}}
        <div class="mt-8 text-center">
            <x-button href="{{ route('activities.index') }}" variant="outline" size="md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Kegiatan
            </x-button>
        </div>
    </div>
</div>
@endsection
