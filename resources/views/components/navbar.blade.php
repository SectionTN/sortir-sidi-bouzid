@php
    $links = [
        ['to' => 'home', 'label' => 'Accueil'],
        ['to' => 'events.index', 'label' => 'Agenda'],
        ['to' => 'events.month', 'label' => 'Ce mois-ci', 'params' => ['year' => now()->year, 'month' => now()->month]],
        ['to' => 'events.past', 'label' => 'Passés'],
        ['to' => 'events.drafts', 'label' => 'Brouillons'],
        ['to' => 'about', 'label' => 'À propos'],
    ];
@endphp

<header class="border-b border-ink/10">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-4 py-4 sm:px-6">
        <a href="{{ route('home') }}" aria-label="Accueil" class="flex items-center gap-2.5">
            <span class="grid grid-cols-2 gap-0.5" aria-hidden="true">
                <span class="size-2.5 rounded-full bg-rose-400"></span>
                <span class="size-2.5 rounded-full bg-amber-400"></span>
                <span class="size-2.5 rounded-full bg-sky-400"></span>
                <span class="size-2.5 rounded-full bg-emerald-400"></span>
            </span>
            <span class="font-display text-lg font-semibold tracking-tight whitespace-nowrap">Sortir à Sidi Bouzid</span>
        </a>

        <nav class="hidden items-center gap-1 lg:flex" aria-label="Navigation principale">
            @foreach ($links as $link)
                <x-nav-link :to="$link['to']" :params="$link['params'] ?? []">{{ $link['label'] }}</x-nav-link>
            @endforeach
        </nav>

        <div class="hidden lg:block">
            <x-button :href="route('events.create')" variant="secondary">
                <x-icon.plus class="fill-current" />
                Proposer un événement
            </x-button>
        </div>

        <details class="group relative lg:hidden">
            <summary class="flex cursor-pointer list-none items-center gap-2 rounded-full px-3 py-2 text-sm font-semibold ring-1 ring-ink/15 select-none hover:ring-ink/30 [&::-webkit-details-marker]:hidden">
                <x-icon.bars-3 class="fill-current" />
                Menu
            </summary>

            <nav class="absolute right-0 z-20 mt-2 flex w-64 flex-col gap-1 rounded-2xl bg-white p-2 shadow-lg ring-1 ring-ink/10" aria-label="Navigation principale">
                @foreach ($links as $link)
                    <x-nav-link :to="$link['to']" :params="$link['params'] ?? []">{{ $link['label'] }}</x-nav-link>
                @endforeach
                <a href="{{ route('events.create') }}" class="mt-1 rounded-xl bg-ink px-3 py-2.5 text-sm font-semibold text-paper">Proposer un événement</a>
            </nav>
        </details>
    </div>
</header>
