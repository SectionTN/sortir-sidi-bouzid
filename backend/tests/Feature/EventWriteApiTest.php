<?php

use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use App\Models\Venue;

beforeEach(function () {
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
        'category_id' => (string) Category::factory()->create()->id,
        'venue_id' => (string) Venue::factory()->create()->id,
        'is_published' => '1',
    ], $overrides);
}

it('creates an event with its tags', function () {
    $tags = Tag::factory(2)->create();

    $response = $this->postJson('/api/events', eventData(['tags' => $tags->pluck('id')->map(fn (int $id) => (string) $id)->all()]))
        ->assertCreated()
        ->assertJsonPath('data.slug', 'soiree-jazz')
        ->assertJsonPath('data.starts_at', '2026-12-12T19:30:00+01:00')
        ->assertJsonCount(2, 'data.tags');

    $event = Event::where('slug', 'soiree-jazz')->first();

    expect($event->title)->toBe('Soirée jazz')
        ->and($event->price)->toBe(15)
        ->and($event->is_published)->toBeTrue()
        ->and($response->json('data.id'))->toBe($event->id);

    $this->assertDatabaseHas('event_tag', ['event_id' => $event->id, 'tag_id' => $tags[0]->id]);
    $this->assertDatabaseHas('event_tag', ['event_id' => $event->id, 'tag_id' => $tags[1]->id]);
});

it('creates a draft when is_published is 0', function () {
    $this->postJson('/api/events', eventData(['is_published' => '0']))
        ->assertCreated()
        ->assertJsonPath('data.is_published', false);
});

it('refuses an event without a title, in French', function () {
    $this->postJson('/api/events', eventData(['title' => '']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title' => 'Le champ titre est obligatoire.']);

    $this->assertDatabaseCount('events', 0);
});

it('refuses a slug with capitals, accents or spaces', function () {
    $this->postJson('/api/events', eventData(['slug' => 'Soirée Malouf']))
        ->assertJsonValidationErrors(['slug' => 'Le slug ne peut contenir que des minuscules sans accent, des chiffres et des tirets.']);

    $this->assertDatabaseCount('events', 0);
});

it('refuses a slug used by another event', function () {
    Event::factory()->create(['slug' => 'soiree-jazz']);

    $this->postJson('/api/events', eventData())
        ->assertJsonValidationErrors(['slug' => 'Ce slug est déjà utilisé par un autre événement.']);
});

it('refuses a slug taken by a page of the site', function (string $slug) {
    $this->postJson('/api/events', eventData(['slug' => $slug]))
        ->assertJsonValidationErrors(['slug' => 'Ce slug est réservé par une page du site.']);
})->with(['create', 'passes', 'brouillons']);

it('refuses a negative price, an unknown venue and a wrong date', function () {
    $this->postJson('/api/events', eventData(['price' => '-5', 'venue_id' => '999999', 'starts_at' => 'demain soir']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['price', 'venue_id', 'starts_at']);

    $this->assertDatabaseCount('events', 0);
});

it('refuses an unknown tag', function () {
    $this->postJson('/api/events', eventData(['tags' => ['999999']]))
        ->assertJsonValidationErrors(['tags.0' => 'La valeur choisie pour le champ tag n\'existe pas.']);
});

it('refuses a category, a venue or a tag that is not a number', function () {
    $this->postJson('/api/events', eventData(['category_id' => 'abc', 'venue_id' => 'abc', 'tags' => ['abc']]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['category_id', 'venue_id', 'tags.0']);
});

it('updates an event and its slug', function () {
    $event = Event::factory()->create(['slug' => 'ancien-slug']);

    $this->putJson('/api/events/ancien-slug', eventData(['slug' => 'nouveau-slug']))
        ->assertOk()
        ->assertJsonPath('data.slug', 'nouveau-slug');

    expect($event->fresh()->slug)->toBe('nouveau-slug');
});

it('keeps its own slug on update', function () {
    $event = Event::factory()->create(['slug' => 'soiree-jazz']);

    $this->putJson('/api/events/soiree-jazz', eventData(['title' => 'Soirée jazz au jardin']))->assertOk();

    expect($event->fresh()->title)->toBe('Soirée jazz au jardin');
});

it('syncs tags on update', function () {
    [$family, $outdoor] = Tag::factory(2)->create();
    $event = Event::factory()->create(['slug' => 'soiree-jazz']);
    $event->tags()->attach([$family->id, $outdoor->id]);

    $this->putJson('/api/events/soiree-jazz', eventData(['tags' => [(string) $family->id]]))->assertOk();
    expect($event->tags()->pluck('tags.id')->all())->toBe([$family->id]);

    $this->putJson('/api/events/soiree-jazz', eventData())->assertOk();
    expect($event->tags()->count())->toBe(0);
});

it('answers 404 when updating or deleting an unknown event', function () {
    $this->putJson('/api/events/inconnu', eventData())->assertNotFound();
    $this->deleteJson('/api/events/inconnu')->assertNotFound();
});

it('toggles the publication', function () {
    Event::factory()->create(['slug' => 'soiree-jazz']);

    $this->patchJson('/api/events/soiree-jazz/publication')
        ->assertOk()
        ->assertJsonPath('data.is_published', true);

    $this->patchJson('/api/events/soiree-jazz/publication')->assertJsonPath('data.is_published', false);
});

it('deletes an event and its tag links', function () {
    $event = Event::factory()->published()->create(['slug' => 'soiree-jazz']);
    $event->tags()->attach(Tag::factory()->create());

    $this->deleteJson('/api/events/soiree-jazz')->assertNoContent();

    $this->assertDatabaseMissing('events', ['id' => $event->id]);
    $this->assertDatabaseMissing('event_tag', ['event_id' => $event->id]);
});
