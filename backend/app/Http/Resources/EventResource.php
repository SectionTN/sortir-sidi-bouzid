<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'description' => $this->description,
            'starts_at' => $this->starts_at->toIso8601String(),
            'price' => $this->price,
            'is_published' => $this->is_published,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'venue' => new VenueResource($this->whenLoaded('venue')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
