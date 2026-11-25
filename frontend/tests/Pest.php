<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(fn () => Http::preventStrayRequests())
    ->in('Feature');

function apiEvent(array $overrides = []): array
{
    return array_replace_recursive([
        'id' => 1,
        'title' => 'Soirée jazz',
        'slug' => 'soiree-jazz',
        'summary' => 'Un trio pour une soirée de standards.',
        'description' => 'Le trio reprend des standards du jazz pendant deux heures.',
        'starts_at' => '2026-12-12T19:30:00+01:00',
        'price' => 15,
        'is_published' => true,
        'category' => ['id' => 1, 'name' => 'Concert', 'slug' => 'concert', 'color' => 'rose'],
        'venue' => ['id' => 1, 'name' => 'Maison de la culture', 'slug' => 'maison-de-la-culture', 'address' => 'Avenue Habib Bourguiba'],
        'tags' => [],
    ], $overrides);
}

function apiPage(array $events, ?int $total = null, int $page = 1, int $perPage = 6): array
{
    $total ??= count($events);

    return [
        'data' => $events,
        'links' => ['first' => null, 'last' => null, 'prev' => null, 'next' => null],
        'meta' => [
            'current_page' => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
            'per_page' => $perPage,
            'total' => $total,
        ],
    ];
}
