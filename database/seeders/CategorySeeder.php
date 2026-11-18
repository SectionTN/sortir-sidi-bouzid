<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()->createMany([
            ['name' => 'Concert', 'slug' => 'concert', 'color' => 'rose'],
            ['name' => 'Atelier', 'slug' => 'atelier', 'color' => 'amber'],
            ['name' => 'Sport', 'slug' => 'sport', 'color' => 'emerald'],
            ['name' => 'Exposition', 'slug' => 'exposition', 'color' => 'sky'],
            ['name' => 'Conférence', 'slug' => 'conference', 'color' => 'violet'],
            ['name' => 'Cinéma', 'slug' => 'cinema', 'color' => 'orange'],
        ]);
    }
}
