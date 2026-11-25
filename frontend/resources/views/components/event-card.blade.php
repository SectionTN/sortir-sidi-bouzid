@use('App\Support\CategoryStyle')

@props(['event'])

<article class="group relative flex flex-col overflow-hidden rounded-3xl bg-white ring-1 ring-ink/10 transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-ink/5 has-[a:focus-visible]:ring-2 has-[a:focus-visible]:ring-ink">
    <div class="flex items-start justify-between gap-3 px-5 pt-5 pb-6 {{ CategoryStyle::for($event['category']['color'], 'solid') }}">
        <x-date-tile :date="$event['starts_at']" />
        <span class="rounded-full bg-white/70 px-2.5 py-1 text-sm font-medium">{{ $event['category']['name'] }}</span>
    </div>

    <div class="flex flex-1 flex-col gap-4 p-5">
        <div>
            @if (! $event['is_published'])
                <p class="mb-2 inline-flex rounded-full bg-ink px-2.5 py-0.5 text-sm font-medium text-paper">Brouillon</p>
            @endif

            <h2 class="font-display text-xl font-semibold tracking-tight text-balance">
                <a href="{{ route('events.show', $event['slug']) }}" class="outline-none after:absolute after:inset-0">{{ $event['title'] }}</a>
            </h2>

            @if ($event['summary'])
                <p class="mt-2 line-clamp-2 text-pretty text-stone-600">{{ $event['summary'] }}</p>
            @endif
        </div>

        <dl class="mt-auto grid gap-1.5 text-sm text-stone-700">
            <div class="flex items-center gap-2">
                <dt class="sr-only">Heure</dt>
                <x-icon.clock class="fill-stone-400" />
                <dd>{{ $event['starts_at']->format('G\hi') }}</dd>
            </div>
            <div class="flex items-center gap-2">
                <dt class="sr-only">Lieu</dt>
                <x-icon.map-pin class="fill-stone-400" />
                <dd class="truncate">{{ $event['venue']['name'] }}</dd>
            </div>
            <div class="flex items-center gap-2">
                <dt class="sr-only">Prix</dt>
                <x-icon.ticket class="fill-stone-400" />
                <dd><x-price :amount="$event['price']" /></dd>
            </div>
        </dl>
    </div>
</article>
