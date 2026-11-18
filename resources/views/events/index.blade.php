<x-layout>
    <x-slot:title>{{ $heading }} - Sortir à Sidi Bouzid</x-slot:title>

    <x-page-header :title="$heading" :subtitle="$subtitle">
        @isset($previousMonth)
            <div class="flex gap-2">
                <x-button :href="route('events.month', ['year' => $previousMonth->year, 'month' => $previousMonth->month])" variant="secondary">
                    <x-icon.chevron-left class="fill-current" />
                    {{ ucfirst($previousMonth->translatedFormat('F')) }}
                </x-button>
                <x-button :href="route('events.month', ['year' => $nextMonth->year, 'month' => $nextMonth->month])" variant="secondary">
                    {{ ucfirst($nextMonth->translatedFormat('F')) }}
                    <x-icon.chevron-right class="fill-current" />
                </x-button>
            </div>
        @endisset
    </x-page-header>

    @if ($events->isEmpty())
        <x-alert type="info" title="Rien pour l'instant">
            Aucun événement à afficher ici pour le moment.
        </x-alert>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($events as $event)
                <x-event-card :event="$event" />
            @endforeach
        </div>
    @endif

    <x-pagination :paginator="$events" />
</x-layout>
