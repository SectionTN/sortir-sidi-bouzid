<?php

use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use App\Models\Venue;

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo('2026-12-05 10:00');
});

it('shows an event with its date, venue, price and tags', function () {
    $venue = Venue::factory()->create(['name' => 'Théâtre de plein air', 'address' => 'Jardin public, Sidi Bouzid']);
    $event = Event::factory()->published()->for($venue)->create([
        'title' => 'Soirée jazz',
        'starts_at' => '2026-12-12 20:30',
        'price' => 15,
    ]);
    $event->tags()->attach(Tag::factory()->createMany([['name' => 'En famille'], ['name' => 'En plein air']]));

    $this->get(route('events.show', $event))
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
    $event = Event::factory()->create(['slug' => 'soiree-jazz']);

    expect(route('events.show', $event))->toEndWith('/events/soiree-jazz');
});

it('shows Gratuit for a free event', function () {
    $event = Event::factory()->published()->create(['price' => 0]);

    $this->get(route('events.show', $event))->assertSee('Gratuit');
});

it('shows a banner on a draft', function () {
    $draft = Event::factory()->create();
    $published = Event::factory()->published()->create();

    $this->get(route('events.show', $draft))->assertSee("Cet événement n'est pas encore publié.", false);
    $this->get(route('events.show', $published))->assertDontSee("Cet événement n'est pas encore publié.", false);
});

it('lists upcoming published events of the same category', function () {
    $concert = Category::factory()->create();
    $event = Event::factory()->published()->for($concert)->create(['title' => 'Soirée jazz']);
    Event::factory()->published()->for($concert)->create(['title' => 'Nuit du malouf', 'starts_at' => '2026-12-18 20:00']);
    Event::factory()->for($concert)->create(['title' => 'Répétition fermée']);
    Event::factory()->published()->for($concert)->create(['title' => 'Vieux concert', 'starts_at' => '2026-12-01 20:00']);
    Event::factory()->published()->create(['title' => 'Tournoi de foot']);

    $related = $this->get(route('events.show', $event))
        ->assertOk()
        ->assertSee('Dans la même catégorie')
        ->assertSee('Nuit du malouf')
        ->assertDontSee('Répétition fermée')
        ->assertDontSee('Vieux concert')
        ->assertDontSee('Tournoi de foot')
        ->viewData('related');

    expect($related->pluck('title')->all())->toBe(['Nuit du malouf']);
});

it('hides the related section when there is nothing to show', function () {
    $event = Event::factory()->published()->create();

    $this->get(route('events.show', $event))->assertDontSee('Dans la même catégorie');
});

it('answers 404 for an unknown event', function () {
    $this->get('/events/evenement-inconnu')->assertNotFound();
});

it('publishes and unpublishes an event', function () {
    $event = Event::factory()->create();

    $this->patch(route('events.publication', $event))
        ->assertRedirect(route('events.show', $event))
        ->assertSessionHas('status');
    expect($event->fresh()->is_published)->toBeTrue();

    $this->patch(route('events.publication', $event));
    expect($event->fresh()->is_published)->toBeFalse();
});

it('deletes an event and its tag links', function () {
    $event = Event::factory()->published()->create();
    $event->tags()->attach(Tag::factory()->create());

    $this->delete(route('events.destroy', $event))
        ->assertRedirect(route('events.index'))
        ->assertSessionHas('status');

    $this->assertDatabaseMissing('events', ['id' => $event->id]);
    $this->assertDatabaseMissing('event_tag', ['event_id' => $event->id]);
});
