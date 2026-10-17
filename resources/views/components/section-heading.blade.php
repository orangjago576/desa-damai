@props([
    'title',
    'subtitle' => null,
    'badge' => null,
    'centered' => false,
    'actionUrl' => null,
    'actionText' => null,
])

<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6 sm:mb-8 {{ $centered ? 'text-center md:text-center' : '' }}">
    <div class="{{ $centered ? 'mx-auto max-w-2xl' : '' }}">
        @if($badge)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-100 text-primary-800 mb-2">
                {{ $badge }}
            </span>
        @endif
        <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-surface-900 tracking-tight">
            {{ $title }}
        </h2>
        @if($subtitle)
            <p class="mt-1.5 text-xs sm:text-sm md:text-base text-surface-600">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    @if($actionUrl && $actionText)
        <div class="flex-shrink-0">
            <a href="{{ $actionUrl }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-primary-700 hover:text-primary-800 group transition-colors">
                <span>{{ $actionText }}</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    @endif
</div>
