@props(['date', 'size' => 'sm'])

<time datetime="{{ $date->toDateString() }}" {{ $attributes->class(['flex items-center gap-3', 'gap-4' => $size === 'lg']) }}>
    <span @class([
        'font-display leading-none font-semibold tracking-tight tabular-nums',
        'text-5xl' => $size === 'sm',
        'text-7xl sm:text-8xl' => $size === 'lg',
    ])>{{ $date->format('j') }}</span>

    <span @class(['flex flex-col font-medium', 'text-sm' => $size === 'sm', 'text-lg' => $size === 'lg'])>
        <span>{{ ucfirst($date->translatedFormat('l')) }}</span>
        <span>{{ $date->translatedFormat('F Y') }}</span>
    </span>
</time>
