@props([
    'variant' => 'primary',
    'size' => 'md',
    'class' => '',
])

@php
    $baseStyles = 'inline-flex items-center font-medium rounded-full border transition-colors';

    $variants = [
        'primary' => 'bg-primary-50 text-primary-800 border-primary-200/80',
        'accent'  => 'bg-accent-50 text-accent-800 border-accent-200/80',
        'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80',
        'warning' => 'bg-amber-50 text-amber-800 border-amber-200/80',
        'danger'  => 'bg-rose-50 text-rose-800 border-rose-200/80',
        'neutral' => 'bg-surface-100 text-surface-700 border-surface-200',
    ];

    $sizes = [
        'sm' => 'px-2 py-0.5 text-[10px] sm:text-xs gap-1',
        'md' => 'px-2.5 py-1 text-xs gap-1.5',
    ];

    $classes = $baseStyles . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . $class;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
