<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        return view('events.index', [
            'heading' => 'Agenda',
            'subtitle' => 'Tous les événements à venir, du plus proche au plus lointain.',
            'events' => Event::published()->upcoming()->with('category', 'venue')->orderBy('starts_at')->paginate(6),
        ]);
    }

    public function past(): View
    {
        return view('events.index', [
            'heading' => 'Événements passés',
            'subtitle' => 'Ce qui a déjà eu lieu, du plus récent au plus ancien.',
            'events' => Event::published()->past()->with('category', 'venue')->orderByDesc('starts_at')->paginate(6),
        ]);
    }

    public function drafts(): View
    {
        return view('events.index', [
            'heading' => 'Brouillons',
            'subtitle' => 'Les événements pas encore publiés. Ils n\'apparaissent nulle part ailleurs sur le site.',
            'events' => Event::where('is_published', false)->with('category', 'venue')->orderBy('starts_at')->paginate(6),
        ]);
    }

    public function month(string $year, string $month): View
    {
        $date = Carbon::create((int) $year, (int) $month, 1);

        return view('events.index', [
            'heading' => ucfirst($date->translatedFormat('F Y')),
            'subtitle' => 'Tous les événements publiés du mois, passés ou à venir.',
            'events' => Event::published()
                ->whereYear('starts_at', $year)
                ->whereMonth('starts_at', $month)
                ->with('category', 'venue')
                ->orderBy('starts_at')
                ->paginate(6),
            'previousMonth' => $date->copy()->subMonth(),
            'nextMonth' => $date->copy()->addMonth(),
        ]);
    }

    public function byCategory(Category $category): View
    {
        return view('events.index', [
            'heading' => $category->name,
            'subtitle' => 'Les prochains événements de la catégorie '.$category->name.'.',
            'events' => $category->events()->published()->upcoming()->with('category', 'venue')->orderBy('starts_at')->paginate(6),
        ]);
    }

    public function byVenue(Venue $venue): View
    {
        return view('events.index', [
            'heading' => $venue->name,
            'subtitle' => $venue->address,
            'events' => $venue->events()->published()->upcoming()->with('category', 'venue')->orderBy('starts_at')->paginate(6),
        ]);
    }

    public function byTag(Tag $tag): View
    {
        return view('events.index', [
            'heading' => $tag->name,
            'subtitle' => 'Les prochains événements marqués « '.$tag->name.' ».',
            'events' => $tag->events()->published()->upcoming()->with('category', 'venue')->orderBy('starts_at')->paginate(6),
        ]);
    }

    public function show(Event $event): View
    {
        $event->load('category', 'venue', 'tags');

        $related = $event->category->events()
            ->published()
            ->upcoming()
            ->where('events.id', '!=', $event->id)
            ->with('category', 'venue')
            ->orderBy('starts_at')
            ->take(3)
            ->get();

        return view('events.show', compact('event', 'related'));
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
}
