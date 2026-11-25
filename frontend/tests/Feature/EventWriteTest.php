<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo('2026-12-05 10:00');
});

function formLists(): array
{
    return [
        'backend.test/api/categories' => Http::response(['data' => [
            ['id' => 1, 'name' => 'Concert', 'slug' => 'concert', 'color' => 'rose', 'events_count' => 0],
        ]]),
        'backend.test/api/venues' => Http::response(['data' => [
            ['id' => 1, 'name' => 'Maison de la culture', 'slug' => 'maison-de-la-culture', 'address' => 'Avenue Habib Bourguiba', 'events_count' => 0],
        ]]),
        'backend.test/api/tags' => Http::response(['data' => [
            ['id' => 3, 'name' => 'En famille', 'slug' => 'en-famille', 'events_count' => 0],
            ['id' => 5, 'name' => 'En plein air', 'slug' => 'en-plein-air', 'events_count' => 0],
        ]]),
    ];
}

function formData(array $overrides = []): array
{
    return array_merge([
        '_token' => 'jeton',
        'title' => 'Soirée jazz',
        'slug' => 'soiree-jazz',
        'summary' => 'Un trio pour une soirée de standards.',
        'description' => 'Le trio reprend des standards du jazz pendant deux heures.',
        'starts_at' => '2026-12-12T19:30',
        'price' => '15',
        'category_id' => '1',
        'venue_id' => '1',
        'is_published' => '1',
    ], $overrides);
}

function refusedWith(array $errors): array
{
    return ['backend.test/api/events' => Http::response(['message' => 'Données invalides.', 'errors' => $errors], 422)] + formLists();
}

it('shows the create form with the categories and venues', function () {
    Http::fake(formLists());

    $this->get(route('events.create'))
        ->assertOk()
        ->assertSee('Proposer un événement')
        ->assertSee('Concert')
        ->assertSee('Maison de la culture')
        ->assertSee('En plein air');
});

it('creates an event and follows its slug', function () {
    Http::fake(['backend.test/api/events' => Http::response(['data' => apiEvent()], 201)]);

    $this->post(route('events.store'), formData(['tags' => ['3', '5']]))
        ->assertRedirect(route('events.show', 'soiree-jazz'))
        ->assertSessionHas('status', 'Événement enregistré.');

    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && $request['title'] === 'Soirée jazz'
        && $request['tags'] === ['3', '5']
        && ! isset($request['_token']));
});

it('creates a draft when the published box is unchecked', function () {
    Http::fake(['backend.test/api/events' => Http::response(['data' => apiEvent(['is_published' => false])], 201)]);

    $this->post(route('events.store'), formData(['is_published' => '0']))->assertRedirect();

    Http::assertSent(fn (Request $request) => $request['is_published'] === '0');
});

it('shows the backend errors in French', function () {
    Http::fake(refusedWith(['title' => ['Le champ titre est obligatoire.']]));

    $this->followingRedirects()
        ->from(route('events.create'))
        ->post(route('events.store'), formData(['title' => '']))
        ->assertOk()
        ->assertSee('Le formulaire contient des erreurs')
        ->assertSee('Le champ titre est obligatoire.');

    Http::assertSent(fn (Request $request) => $request->method() === 'POST' && $request['title'] === null);
});

it('keeps the typed date after a validation error', function () {
    Http::fake(refusedWith(['title' => ['Le champ titre est obligatoire.']]));

    $this->followingRedirects()
        ->from(route('events.create'))
        ->post(route('events.store'), formData(['title' => '', 'starts_at' => '2026-12-24T21:15']))
        ->assertOk()
        ->assertSee('value="2026-12-24T21:15"', false);
});

it('shows a tag error and keeps the tags checked', function () {
    Http::fake(refusedWith(['tags.0' => ['Le tag sélectionné est invalide.']]));

    $this->followingRedirects()
        ->from(route('events.create'))
        ->post(route('events.store'), formData(['tags' => ['3']]))
        ->assertOk()
        ->assertSee('Le tag sélectionné est invalide.')
        ->assertSee('value="3" class="sr-only" checked', false)
        ->assertDontSee('value="5" class="sr-only" checked', false);
});

it('prefills the edit form, date included', function () {
    Http::fake(['backend.test/api/events/soiree-jazz' => Http::response(['data' => apiEvent([
        'tags' => [['id' => 5, 'name' => 'En plein air', 'slug' => 'en-plein-air']],
    ])])] + formLists());

    $this->get(route('events.edit', 'soiree-jazz'))
        ->assertOk()
        ->assertSee('Modifier Soirée jazz')
        ->assertSee('value="2026-12-12T19:30"', false)
        ->assertSee('<option value="1" selected>Concert</option>', false)
        ->assertSee('<option value="1" selected>Maison de la culture</option>', false)
        ->assertSee('value="5" class="sr-only" checked', false)
        ->assertSee('action="'.route('events.update', 'soiree-jazz').'"', false);
});

it('updates an event and follows its new slug', function () {
    Http::fake(['backend.test/api/events/soiree-jazz' => Http::response(['data' => apiEvent(['slug' => 'soiree-jazz-au-jardin'])])]);

    $this->put(route('events.update', 'soiree-jazz'), formData(['_method' => 'PUT', 'slug' => 'soiree-jazz-au-jardin']))
        ->assertRedirect(route('events.show', 'soiree-jazz-au-jardin'))
        ->assertSessionHas('status', 'Modifications enregistrées.');

    Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
        && $request['slug'] === 'soiree-jazz-au-jardin'
        && ! isset($request['_method']));
});

it('publishes and unpublishes an event', function () {
    Http::fake(['backend.test/api/events/soiree-jazz/publication' => Http::sequence()
        ->push(['data' => apiEvent()])
        ->push(['data' => apiEvent(['is_published' => false])])]);

    $this->patch(route('events.publication', 'soiree-jazz'))
        ->assertRedirect(route('events.show', 'soiree-jazz'))
        ->assertSessionHas('status', 'Événement publié : il apparaît maintenant dans l\'agenda.');

    $this->patch(route('events.publication', 'soiree-jazz'))
        ->assertSessionHas('status', 'Événement retiré de l\'agenda : il est maintenant dans les brouillons.');

    Http::assertSentCount(2);
});

it('deletes an event', function () {
    Http::fake(['backend.test/api/events/soiree-jazz' => Http::response(null, 204)]);

    $this->delete(route('events.destroy', 'soiree-jazz'))
        ->assertRedirect(route('events.index'))
        ->assertSessionHas('status', 'Événement supprimé.');

    Http::assertSent(fn (Request $request) => $request->method() === 'DELETE');
});
