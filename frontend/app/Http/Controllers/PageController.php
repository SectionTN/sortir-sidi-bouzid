<?php

namespace App\Http\Controllers;

use App\Services\BackendApi;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(private BackendApi $api)
    {
    }

    public function home(): View
    {
        $upcoming = collect($this->api->events(['per_page' => 7])->items());

        return view('home', [
            'featured' => $upcoming->first(),
            'nextEvents' => $upcoming->skip(1),
            'categories' => $this->api->all('categories'),
            'venues' => $this->api->all('venues'),
            'popularTags' => $this->api->all('tags')
                ->filter(fn (array $tag) => $tag['events_count'] > 0)
                ->sortBy([['events_count', 'desc'], ['name', 'asc']]),
        ]);
    }

    public function about(): View
    {
        return view('about', [
            'author' => 'Rayen Sbai',
            'group' => 'MDW3, classe 3, groupe 1',
        ]);
    }
}
