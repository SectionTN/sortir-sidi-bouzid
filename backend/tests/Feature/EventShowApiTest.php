<?php

use App\Models\Event;
use App\Models\Tag;
use App\Models\Venue;

beforeEach(function () {
    $this->travelTo('2026-12-05 10:00');
});

it('shows an event with its relations and a local ISO date', function () {
    $venue = Venue::factory()->create(['name' => 'Théâtre de plein air', 'address' => 'Jardin public, Sidi Bouzid']);
    $event = Event::factory()->published()->for($venue)->create([
        'slug' => 'soiree-jazz',
        'starts_at' => '2026-12-12 19:30',
        'price' => 15,
    ]);
    $event->tags()->attach(Tag::factory()->createMany([['slug' => 'en-famille'], ['slug' => 'en-plein-air']]));

    $this->getJson('/api/events/soiree-jazz')
        ->assertOk()
        ->assertJsonPath('data.slug', 'soiree-jazz')
        ->assertJsonPath('data.starts_at', '2026-12-12T19:30:00+01:00')
        ->assertJsonPath('data.price', 15)
        ->assertJsonPath('data.venue.address', 'Jardin public, Sidi Bouzid')
        ->assertJsonPath('data.category.slug', $event->category->slug)
        ->assertJsonCount(2, 'data.tags')
        ->assertJsonPath('data.tags.0.slug', 'en-famille');
});

it('keeps the local time of a late event', function () {
    Event::factory()->published()->create(['slug' => 'nuit-du-malouf', 'starts_at' => '2026-12-12 23:30']);

    $this->getJson('/api/events/nuit-du-malouf')->assertJsonPath('data.starts_at', '2026-12-12T23:30:00+01:00');
});

it('shows a draft', function () {
    Event::factory()->create(['slug' => 'repetition-fermee']);

    $this->getJson('/api/events/repetition-fermee')
        ->assertOk()
        ->assertJsonPath('data.is_published', false);
});

it('answers 404 for an unknown event', function () {
    $this->getJson('/api/events/evenement-inconnu')
        ->assertNotFound()
        ->assertJsonStructure(['message']);
});
