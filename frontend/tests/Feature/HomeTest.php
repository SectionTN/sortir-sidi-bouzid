<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo('2026-12-05 10:00');
});

function fakeHomeBackend(array $events = [], array $categories = [], array $tags = []): void
{
    Http::fake([
        'backend.test/api/events*' => Http::response(apiPage($events, perPage: 7)),
        'backend.test/api/categories' => Http::response(['data' => $categories]),
        'backend.test/api/venues' => Http::response(['data' => [
            ['id' => 1, 'name' => 'Maison de la culture', 'slug' => 'maison-de-la-culture', 'address' => 'Avenue Habib Bourguiba', 'events_count' => 4],
        ]]),
        'backend.test/api/tags' => Http::response(['data' => $tags]),
    ]);
}

it('features the next event and lists the ones after it', function () {
    fakeHomeBackend([
        apiEvent(['title' => 'Atelier poterie', 'slug' => 'atelier-poterie', 'starts_at' => '2026-12-06T15:00:00+01:00']),
        apiEvent(['title' => 'Soirée jazz', 'starts_at' => '2026-12-20T19:00:00+01:00']),
    ]);

    $response = $this->get(route('home'))
        ->assertOk()
        ->assertSeeInOrder(['Atelier poterie', 'Soirée jazz'])
        ->assertSee('Maison de la culture');

    expect($response->viewData('featured')['title'])->toBe('Atelier poterie')
        ->and($response->viewData('nextEvents')->pluck('title')->all())->toBe(['Soirée jazz']);

    Http::assertSent(fn (Request $request) => ($request['per_page'] ?? null) == 7 && ! isset($request['status']));
});

it('hides drafts and past events from the home page', function () {
    fakeHomeBackend();

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Aucun événement à venir pour le moment.');
});

it('counts upcoming published events per category', function () {
    fakeHomeBackend(categories: [
        ['id' => 1, 'name' => 'Concert', 'slug' => 'concert', 'color' => 'rose', 'events_count' => 2],
    ]);

    $categories = $this->get(route('home'))
        ->assertOk()
        ->assertSee('2 à venir')
        ->viewData('categories');

    expect($categories->firstWhere('name', 'Concert')['events_count'])->toBe(2);
});

it('hides tags with no upcoming event', function () {
    fakeHomeBackend(tags: [
        ['id' => 1, 'name' => 'En famille', 'slug' => 'en-famille', 'events_count' => 1],
        ['id' => 2, 'name' => 'Étudiants', 'slug' => 'etudiants', 'events_count' => 0],
    ]);

    $tags = $this->get(route('home'))->assertOk()->viewData('popularTags');

    expect($tags->pluck('name')->all())->toBe(['En famille']);
});

it('sorts popular tags by count, then by name', function () {
    fakeHomeBackend(tags: [
        ['id' => 1, 'name' => 'Accès libre', 'slug' => 'acces-libre', 'events_count' => 1],
        ['id' => 2, 'name' => 'En famille', 'slug' => 'en-famille', 'events_count' => 3],
        ['id' => 3, 'name' => 'Débutants', 'slug' => 'debutants', 'events_count' => 1],
    ]);

    $tags = $this->get(route('home'))->assertOk()->viewData('popularTags');

    expect($tags->pluck('name')->values()->all())->toBe(['En famille', 'Accès libre', 'Débutants']);
});

it('shows the about page with the author', function () {
    $this->get(route('about'))
        ->assertOk()
        ->assertSee('Rayen Sbai')
        ->assertSee('MDW3, classe 3, groupe 1')
        ->assertSee('Atelier Framework Côté Serveur')
        ->assertSee('base PostgreSQL')
        ->assertDontSee('SQLite');
});
