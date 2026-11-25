<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VenueResource;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $venues = Venue::withCount(['events' => fn (Builder $query) => $query->published()->upcoming()])
            ->orderBy('name')
            ->get();

        return VenueResource::collection($venues);
    }

    /**
     * Display the specified resource.
     */
    public function show(Venue $venue): VenueResource
    {
        return new VenueResource($venue);
    }
}
