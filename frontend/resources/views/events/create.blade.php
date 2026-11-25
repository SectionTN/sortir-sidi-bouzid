<x-layout>
    <x-slot:title>Proposer un événement - Sortir à Sidi Bouzid</x-slot:title>

    <x-page-header title="Proposer un événement" subtitle="Chaque champ est vérifié par le serveur avant l'enregistrement." />

    @if ($errors->any())
        <x-alert type="error" title="Le formulaire contient des erreurs" class="mb-6">
            Corrigez les champs signalés en rouge, puis envoyez à nouveau.
        </x-alert>
    @endif

    <x-event-form :categories="$categories" :venues="$venues" :tags="$tags" />
</x-layout>
