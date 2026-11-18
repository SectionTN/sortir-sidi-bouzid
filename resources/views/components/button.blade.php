@props(['href' => null, 'variant' => 'primary'])

@php
    $styles = [
        'primary' => 'bg-ink text-paper hover:bg-ink/85',
        'secondary' => 'bg-white text-ink ring-1 ring-ink/15 hover:ring-ink/35',
        'danger' => 'bg-white text-rose-700 ring-1 ring-rose-700/25 hover:bg-rose-50',
        'destructive' => 'bg-rose-700 text-white hover:bg-rose-800',
    ];

    $classes = 'inline-flex items-center justify-center gap-2 rounded-full px-4 py-2.5 text-sm font-semibold select-none transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ink '.($styles[$variant] ?? $styles['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>{{ $slot }}</button>
@endif
