<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo('2026-12-05 10:00');
});

it('lists upcoming published events in date order', function () {
    Http::fake(['backend.test/api/events*' => Http::response(apiPage([
        apiEvent(['title' => 'Atelier poterie', 'slug' => 'atelier-poterie', 'starts_at' => '2026-12-06T18:00:00+01:00']),
        apiEvent(['title' => 'Soirée jazz', 'starts_at' => '2026-12-20T19:00:00+01:00']),
    ]))]);

    $this->get(route('events.index'))
        ->assertOk()
        ->assertSee('Agenda')
        ->assertSeeInOrder(['Atelier poterie', 'Soirée jazz']);

    Http::assertSent(fn (Request $request) => $request->url() === 'http://backend.test/api/events?page=1');
});

it('lists past events newest first', function () {
    Http::fake(['backend.test/api/events*' => Http::response(apiPage([
        apiEvent(['title' => 'Récent tournoi', 'slug' => 'recent-tournoi', 'starts_at' => '2026-12-03T09:00:00+01:00']),
        apiEvent(['title' => 'Ancienne fête', 'slug' => 'ancienne-fete', 'starts_at' => '2026-11-30T20:00:00+01:00']),
    ]))]);

    $this->get(route('events.past'))
        ->assertOk()
        ->assertSee('Événements passés')
        ->assertSeeInOrder(['Récent tournoi', 'Ancienne fête']);

    Http::assertSent(fn (Request $request) => ($request['status'] ?? null) === 'past');
});

it('lists drafts only on the drafts page', function () {
    Http::fake(['backend.test/api/events*' => Http::response(apiPage([
        apiEvent(['title' => 'Répétition fermée', 'slug' => 'repetition-fermee', 'is_published' => false]),
    ]))]);

    $this->get(route('events.drafts'))
        ->assertOk()
        ->assertSee('Répétition fermée')
        ->assertSee('Brouillon');

    Http::assertSent(fn (Request $request) => ($request['status'] ?? null) === 'draft');
});

it('paginates lists by 6', function () {
    $events = collect(range(1, 6))->map(fn (int $i) => apiEvent(['id' => $i, 'slug' => "evenement-{$i}"]))->all();
    Http::fake(['backend.test/api/events*' => Http::response(apiPage($events, total: 8))]);

    $page = $this->get(route('events.index'))
        ->assertOk()
        ->assertSee('/events?page=2')
        ->viewData('events');

    expect($page->count())->toBe(6)
        ->and($page->total())->toBe(8);
});

it('shows the empty state past the last page', function () {
    Http::fake(['backend.test/api/events*' => Http::response(apiPage([], total: 8, page: 3))]);

    $this->get('/events?page=3')
        ->assertOk()
        ->assertSee('Aucun événement à afficher ici pour le moment.');

    Http::assertSent(fn (Request $request) => ($request['page'] ?? null) == 3);
});

it('filters by category', function () {
    Http::fake([
        'backend.test/api/categories/concert' => Http::response(['data' => ['id' => 1, 'name' => 'Concert', 'slug' => 'concert', 'color' => 'rose']]),
        'backend.test/api/events*' => Http::response(apiPage([apiEvent()])),
    ]);

    $this->get(route('events.category', 'concert'))
        ->assertOk()
        ->assertSee('Les prochains événements de la catégorie Concert.')
        ->assertSee('Soirée jazz');

    Http::assertSent(fn (Request $request) => ($request['category'] ?? null) === 'concert');
});

it('answers 404 for an unknown category', function () {
    Http::fake(['backend.test/api/categories/nope' => Http::response(['message' => 'Not found.'], 404)]);

    $this->get('/events/categorie/nope')->assertNotFound();
});

it('filters by venue', function () {
    Http::fake([
        'backend.test/api/venues/theatre-de-plein-air' => Http::response(['data' => [
            'id' => 2, 'name' => 'Théâtre de plein air', 'slug' => 'theatre-de-plein-air', 'address' => 'Jardin public, Sidi Bouzid',
        ]]),
        'backend.test/api/events*' => Http::response(apiPage([apiEvent()])),
    ]);

    $this->get(route('events.venue', 'theatre-de-plein-air'))
        ->assertOk()
        ->assertSee('Théâtre de plein air')
        ->assertSee('Jardin public, Sidi Bouzid')
        ->assertSee('Soirée jazz');

    Http::assertSent(fn (Request $request) => ($request['venue'] ?? null) === 'theatre-de-plein-air');
});

it('filters by tag', function () {
    Http::fake([
        'backend.test/api/tags/en-famille' => Http::response(['data' => ['id' => 3, 'name' => 'En famille', 'slug' => 'en-famille']]),
        'backend.test/api/events*' => Http::response(apiPage([apiEvent()])),
    ]);

    $this->get(route('events.tag', 'en-famille'))
        ->assertOk()
        ->assertSee('Les prochains événements marqués « En famille ».')
        ->assertSee('Soirée jazz');

    Http::assertSent(fn (Request $request) => ($request['tag'] ?? null) === 'en-famille');
});

it('shows every published event of a month', function () {
    Http::fake(['backend.test/api/events*' => Http::response(apiPage([
        apiEvent(['title' => 'Début de mois', 'slug' => 'debut-de-mois', 'starts_at' => '2026-12-02T18:00:00+01:00']),
        apiEvent(['title' => 'Soirée jazz', 'starts_at' => '2026-12-20T19:00:00+01:00']),
    ]))]);

    $this->get(route('events.month', ['year' => 2026, 'month' => 12]))
        ->assertOk()
        ->assertSee('Décembre 2026')
        ->assertSeeInOrder(['Début de mois', 'Soirée jazz'])
        ->assertSee('Novembre')
        ->assertSee('Janvier');

    $this->get('/events/mois/2027/01')
        ->assertOk()
        ->assertSee('Janvier 2027');

    Http::assertSent(fn (Request $request) => ($request['status'] ?? null) === 'published' && ($request['month'] ?? null) === '2026-12');
    Http::assertSent(fn (Request $request) => ($request['month'] ?? null) === '2027-01');
});

it('answers 404 for an invalid month', function (string $uri) {
    $this->get($uri)->assertNotFound();
})->with(['/events/mois/2026/0', '/events/mois/2026/13', '/events/mois/abc/1']);

it('shows the error page when the backend is down', function () {
    Http::fake(['backend.test/*' => Http::failedConnection()]);

    $this->get(route('events.index'))->assertServerError();
});
