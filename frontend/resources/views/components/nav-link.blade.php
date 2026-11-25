@props(['to', 'params' => [], 'match' => null])

@php
    $active = request()->routeIs($match ?? $to);
@endphp

<a
    href="{{ route($to, $params) }}"
    @if ($active) aria-current="page" @endif
    {{ $attributes->class([
        'rounded-full px-3 py-2 text-sm font-medium transition-colors',
        'bg-ink/[0.07] text-ink' => $active,
        'text-stone-600 hover:bg-ink/5 hover:text-ink' => ! $active,
    ]) }}
>
    {{ $slot }}
</a>
