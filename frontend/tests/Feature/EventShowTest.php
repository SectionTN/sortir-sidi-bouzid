<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo('2026-12-05 10:00');
});

function fakeEventPage(array $event, array $related = []): void
{
    Http::fake([
        'backend.test/api/events/'.$event['slug'] => Http::response(['data' => $event]),
        'backend.test/api/events*' => Http::response(apiPage($related, perPage: 4)),
    ]);
}

it('shows an event with its date, venue, price and tags', function () {
    fakeEventPage(apiEvent([
        'starts_at' => '2026-12-12T20:30:00+01:00',
        'price' => 15,
        'venue' => ['name' => 'Théâtre de plein air', 'address' => 'Jardin public, Sidi Bouzid'],
        'tags' => [
            ['id' => 1, 'name' => 'En famille', 'slug' => 'en-famille'],
            ['id' => 2, 'name' => 'En plein air', 'slug' => 'en-plein-air'],
        ],
    ]));

    $this->get(route('events.show', 'soiree-jazz'))
        ->assertOk()
        ->assertSee('Soirée jazz')
        ->assertSee('Samedi 12 décembre 2026')
        ->assertSee('20h30')
        ->assertSee('Théâtre de plein air')
        ->assertSee('Jardin public, Sidi Bouzid')
        ->assertSee('15 DT')
        ->assertSee('En famille')
        ->assertSee('En plein air');
});

it('uses the slug in the event URL', function () {
    Http::fake(['backend.test/api/events*' => Http::response(apiPage([apiEvent()]))]);

    $this->get(route('events.index'))->assertSee(route('events.show', 'soiree-jazz'));
});

it('shows Gratuit for a free event', function () {
    fakeEventPage(apiEvent(['price' => 0]));

    $this->get(route('events.show', 'soiree-jazz'))->assertSee('Gratuit');
});

it('shows a banner on a draft', function () {
    Http::fake([
        'backend.test/api/events/repetition-fermee' => Http::response(['data' => apiEvent(['slug' => 'repetition-fermee', 'is_published' => false])]),
        'backend.test/api/events/soiree-jazz' => Http::response(['data' => apiEvent()]),
        'backend.test/api/events*' => Http::response(apiPage([])),
    ]);

    $this->get(route('events.show', 'repetition-fermee'))->assertOk()->assertSee("Cet événement n'est pas encore publié.", false);
    $this->get(route('events.show', 'soiree-jazz'))->assertOk()->assertDontSee("Cet événement n'est pas encore publié.", false);
});

it('lists upcoming published events of the same category', function () {
    fakeEventPage(apiEvent(), [
        apiEvent(),
        apiEvent(['id' => 2, 'title' => 'Nuit du malouf', 'slug' => 'nuit-du-malouf']),
    ]);

    $related = $this->get(route('events.show', 'soiree-jazz'))
        ->assertOk()
        ->assertSee('Dans la même catégorie')
        ->assertSee('Nuit du malouf')
        ->viewData('related');

    expect($related->pluck('title')->all())->toBe(['Nuit du malouf']);

    Http::assertSent(fn (Request $request) => ($request['category'] ?? null) === 'concert' && ($request['per_page'] ?? null) == 4);
});

it('hides the related section when there is nothing to show', function () {
    fakeEventPage(apiEvent(), [apiEvent()]);

    $this->get(route('events.show', 'soiree-jazz'))->assertOk()->assertDontSee('Dans la même catégorie');
});

it('answers 404 for an unknown event', function () {
    Http::fake(['backend.test/api/events/evenement-inconnu' => Http::response(['message' => 'Not found.'], 404)]);

    $this->get('/events/evenement-inconnu')->assertNotFound();
});

it('shows the local time of a late event', function () {
    fakeEventPage(apiEvent(['starts_at' => '2026-12-12T23:30:00+01:00']));

    $this->get(route('events.show', 'soiree-jazz'))->assertSee('23h30');
});

it('escapes the page title only once', function () {
    fakeEventPage(apiEvent(['title' => "Tournoi d'échecs"]));

    $this->get(route('events.show', 'soiree-jazz'))
        ->assertOk()
        ->assertSee('<title>Tournoi d&#039;échecs - Sortir à Sidi Bouzid</title>', false);
});
