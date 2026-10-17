@props([
    'items' => [],
])

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="flex flex-wrap items-center gap-1.5 text-xs sm:text-sm text-white/70">
        <li>
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1 text-white/70 hover:text-white transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 00-1 1m-6 0h6"/>
                </svg>
                <span>Beranda</span>
            </a>
        </li>

        @foreach($items as $item)
            <li class="flex items-center gap-1.5 text-white/40">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                @if(!empty($item['url']) && !$loop->last)
                    <a href="{{ $item['url'] }}" class="text-white/70 hover:text-white transition-colors truncate max-w-[120px] sm:max-w-[200px]">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-white font-medium truncate max-w-[150px] sm:max-w-[300px]" aria-current="page">
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
