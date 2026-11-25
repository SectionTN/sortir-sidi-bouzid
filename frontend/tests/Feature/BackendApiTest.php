<?php

use App\Services\BackendApi;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

it('reads a page of events with local dates', function () {
    Http::fake([
        'backend.test/api/events*' => Http::response(apiPage([apiEvent(['starts_at' => '2026-12-12T23:30:00+01:00'])], total: 8)),
    ]);

    $events = app(BackendApi::class)->events(['status' => 'past']);

    expect($events->total())->toBe(8)
        ->and($events->first()['starts_at']->format('Y-m-d G\hi'))->toBe('2026-12-12 23h30');

    Http::assertSent(fn (Request $request) => $request['status'] === 'past'
        && $request['page'] == 1
        && $request->hasHeader('Accept', 'application/json'));
});

it('turns a backend 404 into a 404', function () {
    Http::fake(['backend.test/api/events/nope' => Http::response(['message' => 'Not found.'], 404)]);

    expect(fn () => app(BackendApi::class)->event('nope'))->toThrow(NotFoundHttpException::class);
});

it('turns a backend 422 into a validation error', function () {
    Http::fake(['backend.test/api/events' => Http::response([
        'message' => 'Le champ titre est obligatoire.',
        'errors' => ['title' => ['Le champ titre est obligatoire.']],
    ], 422)]);

    expect(fn () => app(BackendApi::class)->createEvent([]))
        ->toThrow(function (ValidationException $exception) {
            expect($exception->errors())->toBe(['title' => ['Le champ titre est obligatoire.']]);
        });
});

it('lists categories', function () {
    Http::fake(['backend.test/api/categories' => Http::response(['data' => [
        ['id' => 2, 'name' => 'Atelier', 'slug' => 'atelier', 'color' => 'amber', 'events_count' => 3],
        ['id' => 1, 'name' => 'Concert', 'slug' => 'concert', 'color' => 'rose', 'events_count' => 5],
    ]])]);

    $categories = app(BackendApi::class)->all('categories');

    expect($categories)->toHaveCount(2)
        ->and($categories->first()['slug'])->toBe('atelier');
});

it('fails loudly when the backend is down', function () {
    Http::fake(['backend.test/*' => Http::failedConnection()]);

    expect(fn () => app(BackendApi::class)->events())->toThrow(ConnectionException::class);
});
