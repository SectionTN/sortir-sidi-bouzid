<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $upcoming = Event::published()->upcoming()->with('category', 'venue')->orderBy('starts_at')->take(7)->get();
        $countUpcoming = fn (Builder $query) => $query->published()->upcoming();

        return view('home', [
            'featured' => $upcoming->first(),
            'nextEvents' => $upcoming->skip(1),
            'categories' => Category::withCount(['events' => $countUpcoming])->orderBy('name')->get(),
            'venues' => Venue::withCount(['events' => $countUpcoming])->orderBy('name')->get(),
            'popularTags' => Tag::withCount(['events' => $countUpcoming])
                ->orderByDesc('events_count')
                ->orderBy('name')
                ->get()
                ->filter(fn (Tag $tag) => $tag->events_count > 0),
        ]);
    }

    public function about(): View
    {
        return view('about', [
            'author' => 'Rayen Sbai',
            'group' => null,
        ]);
    }
}
