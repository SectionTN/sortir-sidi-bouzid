<?php

use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo('2026-12-05 10:00');
});

it('features the next event and lists the ones after it', function () {
    Event::factory()->published()->create(['title' => 'Soirée jazz', 'starts_at' => '2026-12-20 19:00']);
    Event::factory()->published()->create(['title' => 'Atelier poterie', 'starts_at' => '2026-12-06 15:00']);

    $response = $this->get(route('home'))
        ->assertOk()
        ->assertSeeInOrder(['Atelier poterie', 'Soirée jazz']);

    expect($response->viewData('featured')->title)->toBe('Atelier poterie')
        ->and($response->viewData('nextEvents')->pluck('title')->all())->toBe(['Soirée jazz']);
});

it('hides drafts and past events from the home page', function () {
    Event::factory()->create(['title' => 'Répétition fermée']);
    Event::factory()->published()->create(['title' => 'Vieux concert', 'starts_at' => '2026-12-01 20:00']);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('Répétition fermée')
        ->assertDontSee('Vieux concert')
        ->assertSee('Aucun événement à venir pour le moment.');
});

it('counts upcoming published events per category', function () {
    $concert = Category::factory()->create(['name' => 'Concert']);
    Event::factory(2)->published()->upcoming()->for($concert)->create();
    Event::factory()->published()->past()->for($concert)->create();
    Event::factory()->upcoming()->for($concert)->create();

    $categories = $this->get(route('home'))->assertOk()->viewData('categories');

    expect($categories->firstWhere('name', 'Concert')->events_count)->toBe(2);
});

it('hides tags with no upcoming event', function () {
    $family = Tag::factory()->create(['name' => 'En famille']);
    $students = Tag::factory()->create(['name' => 'Étudiants']);
    Event::factory()->published()->upcoming()->hasAttached($family)->create();
    Event::factory()->published()->past()->hasAttached($students)->create();

    $tags = $this->get(route('home'))->assertOk()->viewData('popularTags');

    expect($tags->pluck('name')->all())->toBe(['En famille']);
});

it('removes an unpublished event from the home page', function () {
    $event = Event::factory()->published()->upcoming()->create(['title' => 'Soirée jazz']);
    $this->get(route('home'))->assertSee('Soirée jazz');

    $this->patch(route('events.publication', $event));

    $this->get(route('home'))->assertDontSee('Soirée jazz');
});

it('shows the about page with the author', function () {
    $this->get(route('about'))
        ->assertOk()
        ->assertSee('Rayen Sbai')
        ->assertSee('Atelier Framework Côté Serveur');
});
