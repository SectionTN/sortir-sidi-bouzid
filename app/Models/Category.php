<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    // Full class names, written out so Tailwind finds them when it builds the CSS.
    public const STYLES = [
        'rose' => ['solid' => 'bg-rose-300', 'soft' => 'bg-rose-50'],
        'amber' => ['solid' => 'bg-amber-300', 'soft' => 'bg-amber-50'],
        'emerald' => ['solid' => 'bg-emerald-300', 'soft' => 'bg-emerald-50'],
        'sky' => ['solid' => 'bg-sky-300', 'soft' => 'bg-sky-50'],
        'violet' => ['solid' => 'bg-violet-300', 'soft' => 'bg-violet-50'],
        'orange' => ['solid' => 'bg-orange-300', 'soft' => 'bg-orange-50'],
    ];

    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function style(string $part): string
    {
        return self::STYLES[$this->color][$part] ?? self::STYLES['sky'][$part];
    }
}
