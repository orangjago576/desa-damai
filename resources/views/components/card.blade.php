@props([
    'hover' => false,
    'padding' => 'p-5 sm:p-6',
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-surface-200/80 shadow-sm transition-all duration-200 ' . ($hover ? 'hover:shadow-md hover:border-primary-200 hover:-translate-y-0.5' : '') . ' ' . $padding . ' ' . $class]) }}>
    {{ $slot }}
</div>
