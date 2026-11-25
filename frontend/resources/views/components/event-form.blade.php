@props(['event' => null, 'categories', 'venues', 'tags'])

@php
    $selectedTags = session()->hasOldInput()
        ? old('tags', [])
        : collect($event['tags'] ?? [])->pluck('id')->all();

    $input = 'block w-full rounded-xl bg-white px-3.5 py-2.5 text-base ring-1 ring-ink/15 placeholder:text-stone-400 focus:outline-2 focus:-outline-offset-1 focus:outline-ink aria-invalid:ring-2 aria-invalid:ring-rose-600';
    $select = $input.' col-span-full row-start-1 appearance-none pr-9';
@endphp

<form method="POST" action="{{ $event ? route('events.update', $event['slug']) : route('events.store') }}">
    @csrf

    @if ($event)
        @method('PUT')
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="space-y-6 rounded-3xl bg-white p-6 ring-1 ring-ink/10 sm:p-8 lg:col-span-2">
            <h2 class="font-display text-xl font-semibold tracking-tight">L'événement</h2>

            <x-field label="Titre" for="title">
                <input type="text" id="title" name="title" value="{{ old('title', $event['title'] ?? null) }}" class="{{ $input }}" @error('title') aria-invalid="true" @enderror>
            </x-field>

            <x-field label="Slug" for="slug" help="L'adresse de la page : minuscules sans accent, chiffres et tirets. Exemple : soiree-jazz.">
                <input type="text" id="slug" name="slug" value="{{ old('slug', $event['slug'] ?? null) }}" class="{{ $input }}" @error('slug') aria-invalid="true" @enderror>
            </x-field>

            <x-field label="Résumé" for="summary" help="Facultatif. Une phrase affichée sur les cartes, 255 caractères au plus.">
                <input type="text" id="summary" name="summary" value="{{ old('summary', $event['summary'] ?? null) }}" class="{{ $input }}" @error('summary') aria-invalid="true" @enderror>
            </x-field>

            <x-field label="Description" for="description" help="Au moins 20 caractères. Une ligne vide sépare deux paragraphes.">
                <textarea id="description" name="description" rows="9" class="{{ $input }}" @error('description') aria-invalid="true" @enderror>{{ old('description', $event['description'] ?? null) }}</textarea>
            </x-field>
        </section>

        <div class="space-y-6">
            <section class="space-y-6 rounded-3xl bg-white p-6 ring-1 ring-ink/10 sm:p-8">
                <h2 class="font-display text-xl font-semibold tracking-tight">Quand et où</h2>

                <x-field label="Date et heure" for="starts_at">
                    <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at', $event ? $event['starts_at']->format('Y-m-d\TH:i') : null) }}" class="{{ $input }}" @error('starts_at') aria-invalid="true" @enderror>
                </x-field>

                <x-field label="Prix en dinars" for="price" help="0 pour un événement gratuit.">
                    <input type="number" id="price" name="price" min="0" max="1000" step="1" value="{{ old('price', $event['price'] ?? 0) }}" class="{{ $input }}" @error('price') aria-invalid="true" @enderror>
                </x-field>

                <x-field label="Catégorie" for="category_id">
                    <div class="grid grid-cols-[1fr_--spacing(9)]">
                        <select id="category_id" name="category_id" class="{{ $select }}" @error('category_id') aria-invalid="true" @enderror>
                            <option value="">Choisir une catégorie</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category['id'] }}" @selected(old('category_id', $event['category']['id'] ?? null) == $category['id'])>{{ $category['name'] }}</option>
                            @endforeach
                        </select>
                        <x-icon.chevron-right class="pointer-events-none col-start-2 row-start-1 rotate-90 place-self-center fill-stone-500" />
                    </div>
                </x-field>

                <x-field label="Lieu" for="venue_id">
                    <div class="grid grid-cols-[1fr_--spacing(9)]">
                        <select id="venue_id" name="venue_id" class="{{ $select }}" @error('venue_id') aria-invalid="true" @enderror>
                            <option value="">Choisir un lieu</option>
                            @foreach ($venues as $venue)
                                <option value="{{ $venue['id'] }}" @selected(old('venue_id', $event['venue']['id'] ?? null) == $venue['id'])>{{ $venue['name'] }}</option>
                            @endforeach
                        </select>
                        <x-icon.chevron-right class="pointer-events-none col-start-2 row-start-1 rotate-90 place-self-center fill-stone-500" />
                    </div>
                </x-field>
            </section>

            <section class="space-y-4 rounded-3xl bg-white p-6 ring-1 ring-ink/10 sm:p-8">
                <fieldset>
                    <legend class="font-display text-xl font-semibold tracking-tight">Tags</legend>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($tags as $tag)
                            <label class="inline-flex cursor-pointer items-center rounded-full px-3 py-1.5 text-sm font-medium ring-1 ring-ink/15 select-none hover:ring-ink/35 has-checked:bg-ink has-checked:text-paper has-checked:ring-ink has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-ink">
                                <input type="checkbox" name="tags[]" value="{{ $tag['id'] }}" class="sr-only" @checked(in_array($tag['id'], $selectedTags))>
                                {{ $tag['name'] }}
                            </label>
                        @endforeach
                    </div>
                    @error('tags.*')
                        <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </fieldset>
            </section>

            <section class="rounded-3xl bg-white p-6 ring-1 ring-ink/10 sm:p-8">
                <input type="hidden" name="is_published" value="0">
                <label for="is_published" class="flex cursor-pointer items-start gap-3">
                    <input type="checkbox" id="is_published" name="is_published" value="1" class="mt-1 size-4 shrink-0 accent-ink" @checked(old('is_published', $event['is_published'] ?? null))>
                    <span>
                        <span class="block font-semibold">Publier l'événement</span>
                        <span class="block text-sm text-stone-500">Sinon il reste dans les brouillons, invisible dans l'agenda.</span>
                    </span>
                </label>
            </section>
        </div>
    </div>

    <div class="mt-8 flex flex-wrap items-center gap-3">
        <x-button>{{ $event ? 'Enregistrer les modifications' : 'Enregistrer l\'événement' }}</x-button>
        <x-button :href="$event ? route('events.show', $event['slug']) : route('events.index')" variant="secondary">Annuler</x-button>
    </div>
</form>
