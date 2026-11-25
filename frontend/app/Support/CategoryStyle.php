<?php

namespace App\Support;

class CategoryStyle
{
    // Full class names, written out so Tailwind finds them when it builds the CSS.
    public const STYLES = [
        'rose' => ['solid' => 'bg-rose-300', 'soft' => 'bg-rose-50'],
        'amber' => ['solid' => 'bg-amber-300', 'soft' => 'bg-amber-50'],
        'emerald' => ['solid' => 'bg-emerald-300', 'soft' => 'bg-emerald-50'],
        'sky' => ['solid' => 'bg-sky-300', 'soft' => 'bg-sky-50'],
        'violet' => ['solid' => 'bg-violet-300', 'soft' => 'bg-violet-50'],
        'orange' => ['solid' => 'bg-orange-300', 'soft' => 'bg-orange-50'],
    ];

    public static function for(?string $color, string $part): string
    {
        return self::STYLES[$color][$part] ?? self::STYLES['sky'][$part];
    }
}
