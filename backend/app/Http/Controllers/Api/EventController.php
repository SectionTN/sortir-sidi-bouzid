<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IndexEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexEventRequest $request): AnonymousResourceCollection
    {
        $events = match ($request->validated('status') ?? 'upcoming') {
            'upcoming' => Event::published()->upcoming()->orderBy('starts_at'),
            'past' => Event::published()->past()->orderByDesc('starts_at'),
            'published' => Event::published()->orderBy('starts_at'),
            'draft' => Event::where('is_published', false)->orderBy('starts_at'),
        };

        $events = $events
            ->with('category', 'venue')
            ->when($request->validated('category'), fn (Builder $query, string $slug) => $query->whereRelation('category', 'slug', $slug))
            ->when($request->validated('venue'), fn (Builder $query, string $slug) => $query->whereRelation('venue', 'slug', $slug))
            ->when($request->validated('tag'), fn (Builder $query, string $slug) => $query->whereRelation('tags', 'slug', $slug))
            ->when($request->validated('month'), function (Builder $query, string $month) {
                [$year, $monthNumber] = explode('-', $month);
                $query->whereYear('starts_at', $year)->whereMonth('starts_at', $monthNumber);
            })
            ->orderBy('id')
            ->paginate((int) ($request->validated('per_page') ?? 6))
            ->withQueryString();

        return EventResource::collection($events);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event): EventResource
    {
        return new EventResource($event->load('category', 'venue', 'tags'));
    }
}
