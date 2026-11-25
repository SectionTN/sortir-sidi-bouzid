<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Venue::factory()->createMany([
            ['name' => 'Maison de la culture', 'slug' => 'maison-de-la-culture', 'address' => 'Avenue de la République, Sidi Bouzid'],
            ['name' => 'Théâtre de plein air', 'slug' => 'theatre-de-plein-air', 'address' => 'Jardin public, Sidi Bouzid'],
            ['name' => 'Complexe sportif municipal', 'slug' => 'complexe-sportif-municipal', 'address' => 'Route de Gafsa, Sidi Bouzid'],
            ['name' => 'Bibliothèque régionale', 'slug' => 'bibliotheque-regionale', 'address' => 'Rue des Jasmins, Sidi Bouzid'],
            ['name' => 'Espace jeunes', 'slug' => 'espace-jeunes', 'address' => 'Cité Ennour, Sidi Bouzid'],
            ['name' => 'Galerie du centre-ville', 'slug' => 'galerie-du-centre-ville', 'address' => 'Place du Marché, Sidi Bouzid'],
            ['name' => 'Salle polyvalente du campus', 'slug' => 'salle-polyvalente-du-campus', 'address' => 'Campus universitaire, Sidi Bouzid'],
        ]);
    }
}
