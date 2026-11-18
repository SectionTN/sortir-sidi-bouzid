@props(['title', 'subtitle' => null])

<header class="mb-10 flex flex-wrap items-end justify-between gap-6">
    <div>
        <h1 class="font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl">{{ $title }}</h1>

        @if ($subtitle)
            <p class="mt-3 max-w-[60ch] text-lg text-pretty text-stone-600">{{ $subtitle }}</p>
        @endif
    </div>

    {{ $slot }}
</header>
