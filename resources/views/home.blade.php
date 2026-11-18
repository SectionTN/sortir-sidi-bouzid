<x-layout>
    <section class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
        <div>
            <h1 class="font-display text-5xl font-semibold tracking-tight text-balance sm:text-6xl">Concerts, ateliers, sport : ce qui se passe à Sidi Bouzid.</h1>
            <p class="mt-5 max-w-[48ch] text-lg text-pretty text-stone-600">L'agenda des sorties de la ville, du jour même jusqu'aux semaines à venir. Chacun peut y proposer un événement.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <x-button :href="route('events.index')">
                    Voir l'agenda
                    <x-icon.arrow-right class="fill-current" />
                </x-button>
                <x-button :href="route('events.month', ['year' => now()->year, 'month' => now()->month])" variant="secondary">
                    <x-icon.calendar class="fill-current" />
                    Ce mois-ci
                </x-button>
            </div>
        </div>

        @if ($featured)
            <article class="group relative rounded-3xl p-6 ring-1 ring-ink/10 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-ink/5 has-[a:focus-visible]:ring-2 has-[a:focus-visible]:ring-ink sm:p-8 {{ $featured->category->style('solid') }}">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="font-medium">{{ $featured->starts_at->isToday() ? 'Aujourd\'hui' : 'Prochain événement' }}</p>
                    <span class="rounded-full bg-white/70 px-3 py-1 text-sm font-medium">{{ $featured->category->name }}</span>
                </div>

                <x-date-tile :date="$featured->starts_at" size="lg" class="mt-8" />

                <h2 class="mt-8 font-display text-3xl font-semibold tracking-tight text-balance sm:text-4xl">
                    <a href="{{ route('events.show', $featured) }}" class="outline-none after:absolute after:inset-0">{{ $featured->title }}</a>
                </h2>

                <dl class="mt-6 flex flex-wrap gap-x-6 gap-y-2">
                    <div class="flex items-center gap-2">
                        <dt class="sr-only">Heure</dt>
                        <x-icon.clock class="fill-ink/50" />
                        <dd>{{ $featured->starts_at->format('G\hi') }}</dd>
                    </div>
                    <div class="flex items-center gap-2">
                        <dt class="sr-only">Lieu</dt>
                        <x-icon.map-pin class="fill-ink/50" />
                        <dd>{{ $featured->venue->name }}</dd>
                    </div>
                    <div class="flex items-center gap-2">
                        <dt class="sr-only">Prix</dt>
                        <x-icon.ticket class="fill-ink/50" />
                        <dd>{{ $featured->priceLabel() }}</dd>
                    </div>
                </dl>
            </article>
        @else
            <x-alert type="info" title="Rien de prévu">
                Aucun événement à venir pour le moment.
            </x-alert>
        @endif
    </section>

    @if ($nextEvents->isNotEmpty())
        <section class="mt-20">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <h2 class="font-display text-3xl font-semibold tracking-tight">Ensuite</h2>
                <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 font-medium underline decoration-ink/20 underline-offset-4 hover:decoration-ink">
                    Tout l'agenda
                    <x-icon.arrow-right class="fill-current" />
                </a>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($nextEvents as $event)
                    <x-event-card :event="$event" />
                @endforeach
            </div>
        </section>
    @endif

    <section class="mt-20 grid gap-12 lg:grid-cols-3 lg:gap-16">
        <div class="lg:col-span-2">
            <h2 class="mb-6 font-display text-3xl font-semibold tracking-tight">Par catégorie</h2>

            <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3" aria-label="Catégories">
                @foreach ($categories as $category)
                    <li>
                        <a href="{{ route('events.category', $category) }}" class="flex h-full flex-col justify-between gap-6 rounded-2xl p-4 ring-1 ring-ink/10 transition hover:ring-ink/30 {{ $category->style('soft') }}">
                            <span class="flex items-center gap-2 font-semibold">
                                <span class="size-3 shrink-0 rounded-full {{ $category->style('solid') }}" aria-hidden="true"></span>
                                {{ $category->name }}
                            </span>
                            <span class="text-sm text-stone-600">{{ $category->events_count }} à venir</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            @if ($popularTags->isNotEmpty())
                <h2 class="mt-12 mb-4 font-display text-2xl font-semibold tracking-tight">Tags populaires</h2>

                <ul class="flex flex-wrap gap-2" aria-label="Tags populaires">
                    @foreach ($popularTags as $tag)
                        <li>
                            <a href="{{ route('events.tag', $tag) }}" class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1.5 text-sm font-medium ring-1 ring-ink/10 hover:ring-ink/30">
                                {{ $tag->name }}
                                <span class="text-stone-500">{{ $tag->events_count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div>
            <h2 class="mb-6 font-display text-3xl font-semibold tracking-tight">Par lieu</h2>

            <ul class="divide-y divide-ink/10 rounded-3xl bg-white px-5 ring-1 ring-ink/10" aria-label="Lieux">
                @foreach ($venues as $venue)
                    <li>
                        <a href="{{ route('events.venue', $venue) }}" class="flex items-center justify-between gap-4 py-3.5 hover:text-stone-600">
                            <span class="min-w-0">
                                <span class="block truncate font-medium">{{ $venue->name }}</span>
                                <span class="block truncate text-sm text-stone-500">{{ $venue->address }}</span>
                            </span>
                            <span class="shrink-0 rounded-full bg-paper px-2.5 py-0.5 text-sm tabular-nums">{{ $venue->events_count }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
</x-layout>
