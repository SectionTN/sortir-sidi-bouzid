<?php

namespace App\Http\Controllers;

use App\Services\BackendApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        return view('events.create', $this->formLists());
    }

    public function store(Request $request): RedirectResponse
    {
        $event = $this->api->createEvent($request->except('_token'));

        return redirect()->route('events.show', $event['slug'])->with('status', 'Événement enregistré.');
    }

    public function edit(string $slug): View
    {
        return view('events.edit', ['event' => $this->api->event($slug)] + $this->formLists());
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $event = $this->api->updateEvent($slug, $request->except('_token', '_method'));

        return redirect()->route('events.show', $event['slug'])->with('status', 'Modifications enregistrées.');
    }

    public function destroy(string $slug): RedirectResponse
    {
        $this->api->deleteEvent($slug);

        return redirect()->route('events.index')->with('status', 'Événement supprimé.');
    }

    public function togglePublication(string $slug): RedirectResponse
    {
        $event = $this->api->togglePublication($slug);

        $status = $event['is_published']
            ? 'Événement publié : il apparaît maintenant dans l\'agenda.'
            : 'Événement retiré de l\'agenda : il est maintenant dans les brouillons.';

        return redirect()->route('events.show', $event['slug'])->with('status', $status);
    }

    private function formLists(): array
    {
        return [
            'categories' => $this->api->all('categories'),
            'venues' => $this->api->all('venues'),
            'tags' => $this->api->all('tags'),
        ];
    }
}
