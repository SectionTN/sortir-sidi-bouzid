<?php

use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use App\Models\Venue;

it('splits upcoming and past at the start of today', function () {
    $this->travelTo('2026-12-05 10:00');

    $earlyToday = Event::factory()->published()->create(['starts_at' => '2026-12-05 00:30']);
    $later = Event::factory()->published()->create(['starts_at' => '2026-12-20 19:00']);
    $lateYesterday = Event::factory()->published()->create(['starts_at' => '2026-12-04 23:30']);

    expect(Event::upcoming()->pluck('id')->all())->toEqualCanonicalizing([$earlyToday->id, $later->id])
        ->and(Event::past()->pluck('id')->all())->toBe([$lateYesterday->id]);
});

it('keeps drafts out of published()', function () {
    $published = Event::factory()->published()->create();
    Event::factory()->create();

    expect(Event::published()->pluck('id')->all())->toBe([$published->id]);
});

it('finds an event by its slug', function () {
    $event = Event::factory()->create(['slug' => 'soiree-malouf']);

    expect($event->getRouteKey())->toBe('soiree-malouf');
});

it('links an event to its category, venue and tags', function () {
    $category = Category::factory()->create(['name' => 'Concert']);
    $venue = Venue::factory()->create(['name' => 'Maison de la culture']);
    $tags = Tag::factory(2)->create();

    $event = Event::factory()->for($category)->for($venue)->create();
    $event->tags()->attach($tags);

    expect($event->category->name)->toBe('Concert')
        ->and($event->venue->name)->toBe('Maison de la culture')
        ->and($event->tags)->toHaveCount(2)
        ->and($category->events)->toHaveCount(1)
        ->and($venue->events)->toHaveCount(1)
        ->and($tags->first()->events)->toHaveCount(1);
});

it('seeds upcoming, past and draft events', function () {
    $this->seed();

    expect(Event::published()->upcoming()->count())->toBeGreaterThanOrEqual(12)
        ->and(Event::published()->past()->count())->toBeGreaterThanOrEqual(4)
        ->and(Event::where('is_published', false)->count())->toBeGreaterThanOrEqual(3)
        ->and(Event::whereNull('category_id')->orWhereNull('venue_id')->count())->toBe(0);
});
