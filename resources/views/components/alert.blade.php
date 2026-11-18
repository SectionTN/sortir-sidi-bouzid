@props(['type' => 'info', 'title' => null])

@php
    $styles = [
        'info' => 'bg-sky-50 text-sky-950 ring-sky-900/10',
        'success' => 'bg-emerald-50 text-emerald-950 ring-emerald-900/10',
        'warning' => 'bg-amber-50 text-amber-950 ring-amber-900/10',
        'error' => 'bg-rose-50 text-rose-950 ring-rose-900/15',
    ];

    $classes = $styles[$type] ?? $styles['info'];
@endphp

<div role="{{ $type === 'error' ? 'alert' : 'status' }}" {{ $attributes->merge(['class' => 'rounded-2xl px-5 py-4 ring-1 '.$classes]) }}>
    @if ($title)
        <p class="font-semibold">{{ $title }}</p>
    @endif

    <div>{{ $slot }}</div>
</div>
