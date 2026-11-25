<?php

use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use App\Models\Venue;

beforeEach(function () {
    $this->travelTo('2026-12-05 10:00');
});

it('counts upcoming published events per category, venue and tag', function () {
    $concert = Category::factory()->create(['name' => 'Concert']);
    $hall = Venue::factory()->create(['name' => 'Maison de la culture']);
    $family = Tag::factory()->create(['name' => 'En famille']);

    Event::factory(2)->published()->upcoming()->for($concert)->for($hall)->hasAttached($family)->create();
    Event::factory()->published()->past()->for($concert)->for($hall)->hasAttached($family)->create();
    Event::factory()->upcoming()->for($concert)->for($hall)->hasAttached($family)->create();

    $this->getJson('/api/categories')->assertOk()->assertJsonPath('data.0.events_count', 2);
    $this->getJson('/api/venues')->assertOk()->assertJsonPath('data.0.events_count', 2);
    $this->getJson('/api/tags')->assertOk()->assertJsonPath('data.0.events_count', 2);
});

it('orders categories, venues and tags by name', function () {
    Category::factory()->create(['name' => 'Sport']);
    Category::factory()->create(['name' => 'Atelier']);

    $this->getJson('/api/categories')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Atelier')
        ->assertJsonPath('data.1.name', 'Sport')
        ->assertJsonStructure(['data' => [['id', 'name', 'slug', 'color', 'events_count']]]);
});

it('shows a category, a venue and a tag by slug', function () {
    Category::factory()->create(['name' => 'Concert', 'slug' => 'concert']);
    Venue::factory()->create(['name' => 'Maison de la culture', 'slug' => 'maison-de-la-culture']);
    Tag::factory()->create(['name' => 'En famille', 'slug' => 'en-famille']);

    $this->getJson('/api/categories/concert')
        ->assertOk()
        ->assertJsonPath('data.name', 'Concert')
        ->assertJsonMissingPath('data.events_count');
    $this->getJson('/api/venues/maison-de-la-culture')->assertOk()->assertJsonPath('data.name', 'Maison de la culture');
    $this->getJson('/api/tags/en-famille')->assertOk()->assertJsonPath('data.name', 'En famille');
});

it('answers 404 for an unknown category, venue or tag', function (string $uri) {
    $this->getJson($uri)->assertNotFound();
})->with(['/api/categories/inconnue', '/api/venues/inconnu', '/api/tags/inconnu']);
