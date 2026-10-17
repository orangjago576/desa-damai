@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'class' => '',
])

@php
    $baseStyles = 'inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none';
    
    $variants = [
        'primary'   => 'bg-primary-700 text-white hover:bg-primary-800 focus:ring-primary-600 shadow-sm hover:shadow',
        'secondary' => 'bg-surface-800 text-white hover:bg-surface-900 focus:ring-surface-700 shadow-sm',
        'accent'    => 'bg-accent-600 text-white hover:bg-accent-700 focus:ring-accent-500 shadow-sm',
        'outline'   => 'border border-surface-300 bg-white text-surface-700 hover:bg-surface-50 hover:text-primary-700 focus:ring-primary-500',
        'ghost'     => 'text-surface-600 hover:bg-surface-100 hover:text-surface-900 focus:ring-surface-400',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs gap-1.5',
        'md' => 'px-4 py-2 text-xs sm:text-sm gap-2',
        'lg' => 'px-5 py-2.5 text-sm sm:text-base gap-2.5',
    ];

    $classes = $baseStyles . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . $class;
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
