<x-layout>
    <x-slot:title>Modifier {{ $event->title }}</x-slot:title>

    <x-page-header title="Modifier l'événement" :subtitle="$event->title" />

    @if ($errors->any())
        <x-alert type="error" title="Le formulaire contient des erreurs" class="mb-6">
            Corrigez les champs signalés en rouge, puis envoyez à nouveau.
        </x-alert>
    @endif

    <x-event-form :event="$event" :categories="$categories" :venues="$venues" :tags="$tags" />
</x-layout>
