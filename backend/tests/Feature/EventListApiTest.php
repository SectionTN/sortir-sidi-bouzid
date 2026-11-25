<?php

use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use App\Models\Venue;

beforeEach(function () {
    $this->travelTo('2026-12-05 10:00');
});

it('lists upcoming published events in date order', function () {
    Event::factory()->published()->create(['title' => 'Soirée jazz', 'starts_at' => '2026-12-20 19:00']);
    Event::factory()->published()->create(['title' => 'Atelier poterie', 'starts_at' => '2026-12-06 18:00']);
    Event::factory()->published()->create(['title' => 'Vieux concert', 'starts_at' => '2026-12-01 20:00']);
    Event::factory()->create(['title' => 'Répétition fermée', 'starts_at' => '2026-12-10 20:00']);

    $this->getJson('/api/events')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.title', 'Atelier poterie')
        ->assertJsonPath('data.1.title', 'Soirée jazz')
        ->assertJsonStructure([
            'data' => [['id', 'title', 'slug', 'summary', 'description', 'starts_at', 'price', 'is_published',
                'category' => ['id', 'name', 'slug', 'color'], 'venue' => ['id', 'name', 'slug', 'address']]],
            'links',
            'meta' => ['current_page', 'per_page', 'total'],
        ])
        ->assertJsonMissingPath('data.0.tags');
});

it('lists past events newest first', function () {
    Event::factory()->published()->create(['title' => 'Ancienne fête', 'starts_at' => '2026-11-30 20:00']);
    Event::factory()->published()->create(['title' => 'Récent tournoi', 'starts_at' => '2026-12-03 09:00']);
    Event::factory()->published()->create(['title' => 'Futur ciné', 'starts_at' => '2026-12-12 18:00']);

    $this->getJson('/api/events?status=past')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.title', 'Récent tournoi')
        ->assertJsonPath('data.1.title', 'Ancienne fête');
});

it('lists drafts', function () {
    Event::factory()->create(['title' => 'Répétition fermée']);
    Event::factory()->published()->create(['title' => 'Soirée jazz']);

    $this->getJson('/api/events?status=draft')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Répétition fermée')
        ->assertJsonPath('data.0.is_published', false);
});

it('lists every published event of a month', function () {
    Event::factory()->published()->create(['title' => 'Début de mois', 'starts_at' => '2026-12-02 18:00']);
    Event::factory()->published()->create(['title' => 'Soirée jazz', 'starts_at' => '2026-12-20 19:00']);
    Event::factory()->create(['title' => 'Répétition fermée', 'starts_at' => '2026-12-22 19:00']);
    Event::factory()->published()->create(['title' => 'Fête de janvier', 'starts_at' => '2027-01-10 19:00']);

    $this->getJson('/api/events?status=published&month=2026-12')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.title', 'Début de mois')
        ->assertJsonPath('data.1.title', 'Soirée jazz');
});

it('lists February from the 31st of a month', function () {
    $this->travelTo('2026-12-31 10:00');
    Event::factory()->published()->create(['title' => 'Fête de février', 'starts_at' => '2027-02-14 19:00']);
    Event::factory()->published()->create(['title' => 'Fête de mars', 'starts_at' => '2027-03-03 19:00']);

    $this->getJson('/api/events?status=published&month=2027-02')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Fête de février');
});

it('filters by category', function () {
    $concert = Category::factory()->create(['slug' => 'concert']);
    Event::factory()->published()->for($concert)->create(['title' => 'Soirée jazz', 'starts_at' => '2026-12-20 19:00']);
    Event::factory()->published()->create(['title' => 'Tournoi de foot', 'starts_at' => '2026-12-20 09:00']);

    $this->getJson('/api/events?category=concert')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Soirée jazz');
});

it('filters by venue', function () {
    $garden = Venue::factory()->create(['slug' => 'theatre-de-plein-air']);
    Event::factory()->published()->for($garden)->create(['title' => 'Soirée jazz', 'starts_at' => '2026-12-20 19:00']);
    Event::factory()->published()->create(['title' => 'Tournoi de foot', 'starts_at' => '2026-12-20 09:00']);

    $this->getJson('/api/events?venue=theatre-de-plein-air')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Soirée jazz');
});

it('filters by tag', function () {
    $family = Tag::factory()->create(['slug' => 'en-famille']);
    Event::factory()->published()->hasAttached($family)->create(['title' => 'Soirée jazz', 'starts_at' => '2026-12-20 19:00']);
    Event::factory()->published()->create(['title' => 'Tournoi de foot', 'starts_at' => '2026-12-20 09:00']);

    $this->getJson('/api/events?tag=en-famille')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Soirée jazz');
});

it('refuses an unknown slug, status, month or page size', function (string $query, string $field) {
    $this->getJson('/api/events?'.$query)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);
})->with([
    ['category=inconnue', 'category'],
    ['venue=inconnu', 'venue'],
    ['tag=inconnu', 'tag'],
    ['status=bientot', 'status'],
    ['month=2026-13', 'month'],
    ['per_page=0', 'per_page'],
    ['per_page=51', 'per_page'],
]);

it('paginates by 6 and accepts per_page', function () {
    Event::factory(8)->published()->upcoming()->create();

    $this->getJson('/api/events')
        ->assertOk()
        ->assertJsonCount(6, 'data')
        ->assertJsonPath('meta.total', 8)
        ->assertJsonPath('meta.per_page', 6);

    $this->getJson('/api/events?per_page=7')->assertJsonCount(7, 'data');
});

it('keeps a stable order for events at the same time', function () {
    $ids = Event::factory(3)->published()->create(['starts_at' => '2026-12-20 19:00'])->pluck('id')->sort()->values()->all();

    $response = $this->getJson('/api/events')->assertOk();

    expect(collect($response->json('data'))->pluck('id')->all())->toBe($ids);
});

it('explains a wrong filter in French', function () {
    $this->getJson('/api/events?status=bientot&month=decembre')
        ->assertJsonValidationErrors([
            'status' => 'La valeur du champ statut n\'est pas valide.',
            'month' => 'Le champ mois doit suivre le format Y-m.',
        ]);
});
