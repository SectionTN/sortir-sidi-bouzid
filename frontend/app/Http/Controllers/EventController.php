<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use App\Models\Venue;
use App\Services\BackendApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(private BackendApi $api)
    {
    }

    public function index(): View
    {
        return view('events.index', [
            'heading' => 'Agenda',
            'subtitle' => 'Tous les événements à venir, du plus proche au plus lointain.',
            'events' => $this->api->events(),
        ]);
    }

    public function past(): View
    {
        return view('events.index', [
            'heading' => 'Événements passés',
            'subtitle' => 'Ce qui a déjà eu lieu, du plus récent au plus ancien.',
            'events' => $this->api->events(['status' => 'past']),
        ]);
    }

    public function drafts(): View
    {
        return view('events.index', [
            'heading' => 'Brouillons',
            'subtitle' => 'Les événements pas encore publiés. Ils n\'apparaissent nulle part ailleurs sur le site.',
            'events' => $this->api->events(['status' => 'draft']),
        ]);
    }

    public function month(string $year, string $month): View
    {
        $date = Carbon::create((int) $year, (int) $month, 1);

        return view('events.index', [
            'heading' => ucfirst($date->translatedFormat('F Y')),
            'subtitle' => 'Tous les événements publiés du mois, passés ou à venir.',
            'events' => $this->api->events(['status' => 'published', 'month' => $date->format('Y-m')]),
            'previousMonth' => $date->copy()->subMonth(),
            'nextMonth' => $date->copy()->addMonth(),
        ]);
    }

    public function byCategory(string $slug): View
    {
        $category = $this->api->find('categories', $slug);

        return view('events.index', [
            'heading' => $category['name'],
            'subtitle' => 'Les prochains événements de la catégorie '.$category['name'].'.',
            'events' => $this->api->events(['category' => $slug]),
        ]);
    }

    public function byVenue(string $slug): View
    {
        $venue = $this->api->find('venues', $slug);

        return view('events.index', [
            'heading' => $venue['name'],
            'subtitle' => $venue['address'],
            'events' => $this->api->events(['venue' => $slug]),
        ]);
    }

    public function byTag(string $slug): View
    {
        $tag = $this->api->find('tags', $slug);

        return view('events.index', [
            'heading' => $tag['name'],
            'subtitle' => 'Les prochains événements marqués « '.$tag['name'].' ».',
            'events' => $this->api->events(['tag' => $slug]),
        ]);
    }

    public function show(string $slug): View
    {
        $event = $this->api->event($slug);

        $related = collect($this->api->events(['category' => $event['category']['slug'], 'per_page' => 4])->items())
            ->reject(fn (array $other) => $other['slug'] === $event['slug'])
            ->take(3)
            ->values();

        return view('events.show', compact('event', 'related'));
    }
    public function create(): View
    {
        return view('events.create', [
            'categories' => Category::orderBy('name')->get(),
            'venues' => Venue::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateEvent($request);

        $event = Event::create(Arr::except($validated, ['tags']));
        $event->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('events.show', $event)->with('status', 'Événement enregistré.');
    }

    public function edit(Event $event): View
    {
        $event->load('tags');

        return view('events.edit', [
            'event' => $event,
            'categories' => Category::orderBy('name')->get(),
            'venues' => Venue::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $this->validateEvent($request, $event);

        $event->update(Arr::except($validated, ['tags']));
        $event->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('events.show', $event)->with('status', 'Modifications enregistrées.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('events.index')->with('status', 'Événement supprimé.');
    }

    public function togglePublication(Event $event): RedirectResponse
    {
        $event->update(['is_published' => ! $event->is_published]);

        $status = $event->is_published
            ? 'Événement publié : il apparaît maintenant dans l\'agenda.'
            : 'Événement retiré de l\'agenda : il est maintenant dans les brouillons.';

        return redirect()->route('events.show', $event)->with('status', $status);
    }

    private function validateEvent(Request $request, ?Event $event = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::notIn(['create', 'passes', 'brouillons']),
                Rule::unique('events', 'slug')->ignore($event),
            ],
            'summary' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'starts_at' => ['required', 'date'],
            'price' => ['required', 'integer', 'min:0', 'max:1000'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'venue_id' => ['required', 'integer', 'exists:venues,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        return $validated;
    }
}
