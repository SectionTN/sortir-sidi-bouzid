<x-layout>
    <x-slot:title>À propos - Sortir à Sidi Bouzid</x-slot:title>

    <x-page-header title="À propos" subtitle="Un projet de l'Atelier Framework Côté Serveur, ISET Sidi Bouzid." />

    <div class="max-w-[65ch] space-y-5 text-lg leading-relaxed text-stone-800">
        <p>Sortir à Sidi Bouzid est un agenda des sorties de la ville : concerts, ateliers, sport, expositions, conférences et cinéma. On y parcourt les événements par date, par catégorie, par lieu ou par tag, et chacun peut en proposer un.</p>

        <p>Le site est fait de deux applications Laravel. Le frontend affiche les pages avec des routes, des contrôleurs, des vues Blade et des composants. Il demande les données au backend, une API JSON qui valide les formulaires et lit une base PostgreSQL avec Eloquent. Les deux applications sont testées avec Pest.</p>

        <dl class="divide-y divide-ink/10 rounded-3xl bg-white px-6 text-base ring-1 ring-ink/10">
            <div class="flex justify-between gap-4 py-4">
                <dt class="text-stone-500">Auteur</dt>
                <dd class="font-medium">{{ $author }}</dd>
            </div>
            @if ($group)
                <div class="flex justify-between gap-4 py-4">
                    <dt class="text-stone-500">Groupe</dt>
                    <dd class="font-medium">{{ $group }}</dd>
                </div>
            @endif
            <div class="flex justify-between gap-4 py-4">
                <dt class="text-stone-500">Matière</dt>
                <dd class="text-right font-medium">Atelier Framework Côté Serveur</dd>
            </div>
        </dl>

        <x-alert type="info" title="Données de démonstration">
            Les événements, les lieux et les dates sont inventés pour la démonstration. Les dates sont calculées à partir du jour où la base est remplie.
        </x-alert>
    </div>
</x-layout>
