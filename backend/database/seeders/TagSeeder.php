<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::factory()->createMany([
            ['name' => 'En famille', 'slug' => 'en-famille'],
            ['name' => 'Étudiants', 'slug' => 'etudiants'],
            ['name' => 'En plein air', 'slug' => 'en-plein-air'],
            ['name' => 'Sur réservation', 'slug' => 'sur-reservation'],
            ['name' => 'Débutants bienvenus', 'slug' => 'debutants-bienvenus'],
        ]);
    }
}
