<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class BackendApi
{
    public function events(array $query = []): LengthAwarePaginator
    {
        $query += ['page' => Paginator::resolveCurrentPage()];

        $json = $this->check($this->request()->get('/api/events', $query))->json();

        return new LengthAwarePaginator(
            array_map($this->toEvent(...), $json['data']),
            $json['meta']['total'],
            $json['meta']['per_page'],
            $json['meta']['current_page'],
            ['path' => request()->url(), 'query' => request()->query()],
        );
    }

    public function event(string $slug): array
    {
        return $this->toEvent($this->check($this->request()->get($this->eventUrl($slug)))->json('data'));
    }

    public function createEvent(array $input): array
    {
        return $this->toEvent($this->check($this->request()->post('/api/events', $input))->json('data'));
    }

    public function updateEvent(string $slug, array $input): array
    {
        return $this->toEvent($this->check($this->request()->put($this->eventUrl($slug), $input))->json('data'));
    }

    public function togglePublication(string $slug): array
    {
        return $this->toEvent($this->check($this->request()->patch($this->eventUrl($slug).'/publication'))->json('data'));
    }

    public function deleteEvent(string $slug): void
    {
        $this->check($this->request()->delete($this->eventUrl($slug)));
    }

    public function all(string $resource): Collection
    {
        return collect($this->check($this->request()->get("/api/{$resource}"))->json('data'));
    }

    public function find(string $resource, string $slug): array
    {
        return $this->check($this->request()->get("/api/{$resource}/".rawurlencode($slug)))->json('data');
    }

    private function request(): PendingRequest
    {
        return Http::baseUrl(config('services.backend.url'))->acceptJson();
    }

    private function check(Response $response): Response
    {
        if ($response->notFound()) {
            abort(404);
        }

        if ($response->unprocessableEntity()) {
            throw ValidationException::withMessages($response->json('errors'));
        }

        return $response->throw();
    }

    private function eventUrl(string $slug): string
    {
        return '/api/events/'.rawurlencode($slug);
    }

    private function toEvent(array $event): array
    {
        $event['starts_at'] = Carbon::parse($event['starts_at'])->setTimezone(config('app.timezone'));

        return $event;
    }
}
