<x-layout>
    <x-slot:title>{{ $event->title }}</x-slot:title>

    <a href="{{ route('events.index') }}" class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-stone-600 hover:text-ink">
        <x-icon.arrow-left class="fill-current" />
        Retour à l'agenda
    </a>

    <article>
        <header class="rounded-3xl px-6 py-8 sm:px-10 sm:py-12 {{ $event->category->style('solid') }}">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('events.category', $event->category) }}" class="rounded-full bg-white/70 px-3 py-1 text-sm font-medium hover:bg-white">{{ $event->category->name }}</a>

                @if (! $event->is_published)
                    <p class="rounded-full bg-ink px-3 py-1 text-sm font-medium text-paper">Cet événement n'est pas encore publié.</p>
                @endif
            </div>

            <h1 class="mt-6 max-w-[20ch] font-display text-4xl font-semibold tracking-tight text-balance sm:text-6xl">{{ $event->title }}</h1>

            @if ($event->summary)
                <p class="mt-4 max-w-[55ch] text-lg text-pretty sm:text-xl">{{ $event->summary }}</p>
            @endif

            <x-date-tile :date="$event->starts_at" size="lg" class="mt-8" />
        </header>

        <div class="mt-10 grid gap-10 lg:grid-cols-3 lg:gap-16">
            <div class="max-w-[65ch] text-lg leading-relaxed whitespace-pre-line text-stone-800 lg:col-span-2">{{ $event->description }}</div>

            <aside class="space-y-8">
                <dl class="divide-y divide-ink/10 rounded-3xl bg-white px-6 ring-1 ring-ink/10">
                    <div class="flex gap-3 py-4">
                        <x-icon.calendar class="mt-1 fill-stone-400" />
                        <div>
                            <dt class="text-sm text-stone-500">Date</dt>
                            <dd class="font-medium">{{ ucfirst($event->starts_at->translatedFormat('l j F Y')) }}</dd>
                        </div>
                    </div>
                    <div class="flex gap-3 py-4">
                        <x-icon.clock class="mt-1 fill-stone-400" />
                        <div>
                            <dt class="text-sm text-stone-500">Heure</dt>
                            <dd class="font-medium">{{ $event->starts_at->format('G\hi') }}</dd>
                        </div>
                    </div>
                    <div class="flex gap-3 py-4">
                        <x-icon.map-pin class="mt-1 fill-stone-400" />
                        <div>
                            <dt class="text-sm text-stone-500">Lieu</dt>
                            <dd>
                                <a href="{{ route('events.venue', $event->venue) }}" class="font-medium underline decoration-ink/20 underline-offset-4 hover:decoration-ink">{{ $event->venue->name }}</a>
                                <p class="text-stone-600">{{ $event->venue->address }}</p>
                            </dd>
                        </div>
                    </div>
                    <div class="flex gap-3 py-4">
                        <x-icon.ticket class="mt-1 fill-stone-400" />
                        <div>
                            <dt class="text-sm text-stone-500">Entrée</dt>
                            <dd class="font-medium">{{ $event->priceLabel() }}</dd>
                        </div>
                    </div>
                </dl>

                @if ($event->tags->isNotEmpty())
                    <ul class="flex flex-wrap gap-2" aria-label="Tags de l'événement">
                        @foreach ($event->tags as $tag)
                            <li>
                                <a href="{{ route('events.tag', $tag) }}" class="inline-flex rounded-full bg-white px-3 py-1.5 text-sm font-medium ring-1 ring-ink/10 hover:ring-ink/30">{{ $tag->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="flex flex-wrap items-start gap-2">
                    <x-button :href="route('events.edit', $event)" variant="secondary">
                        <x-icon.pencil-square class="fill-current" />
                        Modifier
                    </x-button>

                    <form method="POST" action="{{ route('events.publication', $event) }}">
                        @csrf
                        @method('PATCH')

                        @if ($event->is_published)
                            <x-button variant="secondary">
                                <x-icon.eye-slash class="fill-current" />
                                Dépublier
                            </x-button>
                        @else
                            <x-button>
                                <x-icon.eye class="fill-current" />
                                Publier
                            </x-button>
                        @endif
                    </form>

                    <details class="group w-full">
                        <summary class="inline-flex cursor-pointer list-none items-center gap-2 rounded-full bg-white px-4 py-2.5 text-sm font-semibold text-rose-700 ring-1 ring-rose-700/25 select-none hover:bg-rose-50 [&::-webkit-details-marker]:hidden">
                            <x-icon.trash class="fill-current" />
                            Supprimer
                        </summary>

                        <form method="POST" action="{{ route('events.destroy', $event) }}" class="mt-3 rounded-2xl bg-rose-50 p-4 ring-1 ring-rose-900/15">
                            @csrf
                            @method('DELETE')

                            <p class="mb-3 text-rose-950">Cette action est définitive : l'événement sera effacé de la base.</p>
                            <x-button variant="destructive">Confirmer la suppression</x-button>
                        </form>
                    </details>
                </div>
            </aside>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="mt-20 border-t border-ink/10 pt-10">
            <h2 class="mb-6 font-display text-2xl font-semibold tracking-tight">Dans la même catégorie</h2>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($related as $relatedEvent)
                    <x-event-card :event="$relatedEvent" />
                @endforeach
            </div>
        </section>
    @endif
</x-layout>
