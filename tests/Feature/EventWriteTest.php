<?php

use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use App\Models\Venue;

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo('2026-12-05 10:00');
});

function eventData(array $overrides = []): array
{
    return array_merge([
        'title' => 'Soirée jazz',
        'slug' => 'soiree-jazz',
        'summary' => 'Un trio pour une soirée de standards.',
        'description' => 'Le trio reprend des standards du jazz pendant deux heures.',
        'starts_at' => '2026-12-12T19:30',
        'price' => '15',
        'category_id' => Category::factory()->create()->id,
        'venue_id' => Venue::factory()->create()->id,
        'is_published' => '1',
    ], $overrides);
}

it('shows the create form with the categories and venues', function () {
    Category::factory()->create(['name' => 'Concert']);
    Venue::factory()->create(['name' => 'Maison de la culture']);

    $this->get(route('events.create'))
        ->assertOk()
        ->assertSee('Proposer un événement')
        ->assertSee('Concert')
        ->assertSee('Maison de la culture');
});

it('creates an event with its tags', function () {
    $tags = Tag::factory(2)->create();

    $this->post(route('events.store'), eventData(['tags' => $tags->pluck('id')->all()]))
        ->assertRedirect(route('events.show', 'soiree-jazz'))
        ->assertSessionHas('status');

    $event = Event::where('slug', 'soiree-jazz')->first();

    expect($event->title)->toBe('Soirée jazz')
        ->and($event->starts_at->format('Y-m-d H:i'))->toBe('2026-12-12 19:30')
        ->and($event->price)->toBe(15)
        ->and($event->is_published)->toBeTrue()
        ->and($event->tags()->count())->toBe(2);
});

it('creates a draft when the published box is unchecked', function () {
    $this->post(route('events.store'), eventData(['is_published' => '0']));

    expect(Event::first()->is_published)->toBeFalse();
});

it('refuses an event without a title, in French', function () {
    $this->from(route('events.create'))
        ->post(route('events.store'), eventData(['title' => '']))
        ->assertRedirect(route('events.create'))
        ->assertSessionHasErrors(['title' => 'Le champ titre est obligatoire.']);

    $this->assertDatabaseCount('events', 0);
});

it('refuses a slug with capitals, accents or spaces', function () {
    $this->post(route('events.store'), eventData(['slug' => 'Soirée Malouf']))
        ->assertSessionHasErrors('slug');

    $this->assertDatabaseCount('events', 0);
});

it('refuses a slug used by another event', function () {
    Event::factory()->create(['slug' => 'soiree-jazz']);

    $this->post(route('events.store'), eventData())
        ->assertSessionHasErrors(['slug' => 'Ce slug est déjà utilisé par un autre événement.']);
});

it('refuses a slug taken by a page of the site', function (string $slug) {
    $this->post(route('events.store'), eventData(['slug' => $slug]))
        ->assertSessionHasErrors('slug');
})->with(['create', 'passes', 'brouillons']);

it('refuses a negative price, an unknown venue and a wrong date', function () {
    $this->post(route('events.store'), eventData(['price' => '-5', 'venue_id' => 999, 'starts_at' => 'demain soir']))
        ->assertSessionHasErrors(['price', 'venue_id', 'starts_at']);

    $this->assertDatabaseCount('events', 0);
});

it('prefills the edit form, date included', function () {
    $event = Event::factory()->create(['title' => 'Soirée jazz', 'starts_at' => '2026-12-12 19:30']);

    $this->get(route('events.edit', $event))
        ->assertOk()
        ->assertSee('Soirée jazz')
        ->assertSee('value="2026-12-12T19:30"', false);
});

it('keeps the typed date after a validation error', function () {
    $this->followingRedirects()
        ->from(route('events.create'))
        ->post(route('events.store'), eventData(['title' => '', 'starts_at' => '2026-12-24T21:15']))
        ->assertSee('Le champ titre est obligatoire.')
        ->assertSee('value="2026-12-24T21:15"', false);
});

it('updates an event and follows its new slug', function () {
    $event = Event::factory()->create(['slug' => 'ancien-slug']);

    $this->put(route('events.update', $event), eventData(['slug' => 'nouveau-slug']))
        ->assertRedirect(route('events.show', 'nouveau-slug'));

    expect($event->fresh()->slug)->toBe('nouveau-slug');
});

it('keeps its own slug on update', function () {
    $event = Event::factory()->create(['slug' => 'soiree-jazz']);

    $this->put(route('events.update', $event), eventData(['title' => 'Soirée jazz au jardin']))
        ->assertSessionHasNoErrors();

    expect($event->fresh()->title)->toBe('Soirée jazz au jardin');
});

it('syncs tags on update', function () {
    [$family, $outdoor] = Tag::factory(2)->create();
    $event = Event::factory()->create(['slug' => 'soiree-jazz']);
    $event->tags()->attach([$family->id, $outdoor->id]);

    $this->put(route('events.update', $event), eventData(['tags' => [$family->id]]));
    expect($event->tags()->pluck('tags.id')->all())->toBe([$family->id]);

    $this->put(route('events.update', $event), eventData());
    expect($event->tags()->count())->toBe(0);
});
