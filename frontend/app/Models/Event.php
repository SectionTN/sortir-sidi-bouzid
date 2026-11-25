<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'description',
        'starts_at',
        'price',
        'is_published',
        'category_id',
        'venue_id',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'price' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function priceLabel(): string
    {
        return $this->price === 0 ? 'Gratuit' : $this->price.' DT';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function scopeUpcoming(Builder $query): void
    {
        $query->where('starts_at', '>=', today());
    }

    public function scopePast(Builder $query): void
    {
        $query->where('starts_at', '<', today());
    }
}
