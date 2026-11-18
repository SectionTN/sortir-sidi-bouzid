<?php

use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use App\Models\Venue;

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo('2026-12-05 10:00');
});

it('lists upcoming published events in date order', function () {
    Event::factory()->published()->create(['title' => 'Soirée jazz', 'starts_at' => '2026-12-20 19:00']);
    Event::factory()->published()->create(['title' => 'Atelier poterie', 'starts_at' => '2026-12-06 18:00']);
    Event::factory()->published()->create(['title' => 'Vieux concert', 'starts_at' => '2026-12-01 20:00']);
    Event::factory()->create(['title' => 'Répétition fermée', 'starts_at' => '2026-12-10 20:00']);

    $this->get(route('events.index'))
        ->assertOk()
        ->assertSeeInOrder(['Atelier poterie', 'Soirée jazz'])
        ->assertDontSee('Vieux concert')
        ->assertDontSee('Répétition fermée');
});

it('lists past events newest first', function () {
    Event::factory()->published()->create(['title' => 'Ancienne fête', 'starts_at' => '2026-11-30 20:00']);
    Event::factory()->published()->create(['title' => 'Récent tournoi', 'starts_at' => '2026-12-03 09:00']);
    Event::factory()->published()->create(['title' => 'Futur ciné', 'starts_at' => '2026-12-12 18:00']);

    $this->get(route('events.past'))
        ->assertOk()
        ->assertSeeInOrder(['Récent tournoi', 'Ancienne fête'])
        ->assertDontSee('Futur ciné');
});

it('lists drafts only on the drafts page', function () {
    Event::factory()->create(['title' => 'Répétition fermée']);
    Event::factory()->published()->create(['title' => 'Soirée jazz']);

    $this->get(route('events.drafts'))
        ->assertOk()
        ->assertSee('Répétition fermée')
        ->assertDontSee('Soirée jazz');

    $this->get(route('events.index'))->assertDontSee('Répétition fermée');
});

it('paginates lists by 6', function () {
    Event::factory(8)->published()->upcoming()->create();

    $events = $this->get(route('events.index'))->assertOk()->viewData('events');

    expect($events->count())->toBe(6)
        ->and($events->total())->toBe(8);
});

it('filters by category', function () {
    $concert = Category::factory()->create(['name' => 'Concert', 'slug' => 'concert']);
    Event::factory()->published()->for($concert)->create(['title' => 'Soirée jazz', 'starts_at' => '2026-12-20 19:00']);
    Event::factory()->for($concert)->create(['title' => 'Répétition fermée', 'starts_at' => '2026-12-21 19:00']);
    Event::factory()->published()->for($concert)->create(['title' => 'Vieux concert', 'starts_at' => '2026-12-01 20:00']);
    Event::factory()->published()->create(['title' => 'Tournoi de foot', 'starts_at' => '2026-12-20 09:00']);

    $this->get(route('events.category', $concert))
        ->assertOk()
        ->assertSee('Concert')
        ->assertSee('Soirée jazz')
        ->assertDontSee('Répétition fermée')
        ->assertDontSee('Vieux concert')
        ->assertDontSee('Tournoi de foot');
});

it('filters by venue', function () {
    $garden = Venue::factory()->create(['name' => 'Théâtre de plein air', 'slug' => 'theatre-de-plein-air']);
    Event::factory()->published()->for($garden)->create(['title' => 'Soirée jazz', 'starts_at' => '2026-12-20 19:00']);
    Event::factory()->published()->create(['title' => 'Tournoi de foot', 'starts_at' => '2026-12-20 09:00']);

    $this->get(route('events.venue', $garden))
        ->assertOk()
        ->assertSee('Théâtre de plein air')
        ->assertSee('Soirée jazz')
        ->assertDontSee('Tournoi de foot');
});

it('filters by tag', function () {
    $family = Tag::factory()->create(['name' => 'En famille', 'slug' => 'en-famille']);
    Event::factory()->published()->hasAttached($family)->create(['title' => 'Soirée jazz', 'starts_at' => '2026-12-20 19:00']);
    Event::factory()->published()->create(['title' => 'Tournoi de foot', 'starts_at' => '2026-12-20 09:00']);

    $this->get(route('events.tag', $family))
        ->assertOk()
        ->assertSee('En famille')
        ->assertSee('Soirée jazz')
        ->assertDontSee('Tournoi de foot');
});

it('shows every published event of a month', function () {
    Event::factory()->published()->create(['title' => 'Début de mois', 'starts_at' => '2026-12-02 18:00']);
    Event::factory()->published()->create(['title' => 'Soirée jazz', 'starts_at' => '2026-12-20 19:00']);
    Event::factory()->create(['title' => 'Répétition fermée', 'starts_at' => '2026-12-22 19:00']);
    Event::factory()->published()->create(['title' => 'Fête de janvier', 'starts_at' => '2027-01-10 19:00']);

    $this->get(route('events.month', ['year' => 2026, 'month' => 12]))
        ->assertOk()
        ->assertSee('Décembre 2026')
        ->assertSeeInOrder(['Début de mois', 'Soirée jazz'])
        ->assertDontSee('Répétition fermée')
        ->assertDontSee('Fête de janvier');

    $this->get('/events/mois/2027/01')
        ->assertOk()
        ->assertSee('Janvier 2027')
        ->assertSee('Fête de janvier');
});

it('answers 404 for an invalid month', function (string $uri) {
    $this->get($uri)->assertNotFound();
})->with(['/events/mois/2026/0', '/events/mois/2026/13', '/events/mois/abc/1']);
