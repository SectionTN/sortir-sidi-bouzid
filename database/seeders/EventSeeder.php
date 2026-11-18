<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    /**
     * Fictional events. "days" counts from today: negative means past.
     */
    private const EVENTS = [
        [
            'title' => 'Club de lecture : romans de l\'été',
            'category' => 'conference', 'venue' => 'bibliotheque-regionale', 'days' => 0, 'time' => '18:00', 'price' => 0, 'published' => true,
            'tags' => ['en-famille', 'debutants-bienvenus'],
            'summary' => 'Une heure pour parler des livres qui nous ont marqués cet été, autour d\'un thé.',
            'description' => "Chacun apporte un roman lu pendant l'été et le présente en quelques minutes. Pas besoin d'avoir tout lu : l'idée est de donner envie aux autres.\n\nLe club se réunit chaque mois dans la salle de lecture de la bibliothèque. Les nouveaux venus sont les bienvenus.",
        ],
        [
            'title' => 'Exposition : Les couleurs de la steppe',
            'category' => 'exposition', 'venue' => 'galerie-du-centre-ville', 'days' => 1, 'time' => '10:00', 'price' => 0, 'published' => true,
            'tags' => ['en-famille'],
            'summary' => 'Quarante photographies des paysages de la région, du lever du jour aux récoltes.',
            'description' => "Trois photographes amateurs ont parcouru la région pendant un an. Leurs images montrent les champs d'oliviers, les marchés du matin et les ciels d'orage de la steppe.\n\nL'exposition reste ouverte tous les jours de 10 h à 18 h pendant trois semaines.",
        ],
        [
            'title' => 'Soirée malouf au clair de lune',
            'category' => 'concert', 'venue' => 'theatre-de-plein-air', 'days' => 2, 'time' => '20:30', 'price' => 10, 'published' => true,
            'tags' => ['en-famille', 'en-plein-air'],
            'summary' => 'Un orchestre de huit musiciens joue les grands airs du malouf sous les étoiles.',
            'description' => "L'orchestre de la Maison de la culture présente un programme de malouf, la musique arabo-andalouse de Tunisie, avec oud, violon, qanoun et percussions.\n\nLe concert a lieu en plein air. Pensez à prendre une veste, les soirées sont fraîches.",
        ],
        [
            'title' => 'Atelier de poterie pour débutants',
            'category' => 'atelier', 'venue' => 'maison-de-la-culture', 'days' => 3, 'time' => '15:00', 'price' => 15, 'published' => true,
            'tags' => ['debutants-bienvenus', 'sur-reservation'],
            'summary' => 'Apprenez à monter un bol au colombin et repartez avec votre pièce.',
            'description' => "Une potière de la région montre les gestes de base : préparer la terre, monter un bol au colombin, lisser et décorer.\n\nL'argile et les outils sont fournis. Le groupe est limité à douze personnes, la réservation se fait à l'accueil de la Maison de la culture.",
        ],
        [
            'title' => 'Tournoi de football à cinq',
            'category' => 'sport', 'venue' => 'complexe-sportif-municipal', 'days' => 5, 'time' => '09:00', 'price' => 0, 'published' => true,
            'tags' => ['etudiants', 'en-plein-air'],
            'summary' => 'Seize équipes, des matchs de vingt minutes et une finale en fin d\'après-midi.',
            'description' => "Le tournoi est ouvert aux équipes de cinq joueurs et deux remplaçants. Les matchs de poule se jouent le matin, les phases finales l'après-midi.\n\nLes inscriptions se font sur place jusqu'à 8 h 30. Chaque équipe doit avoir un maillot de la même couleur.",
        ],
        [
            'title' => 'Ciné-club : courts métrages tunisiens',
            'category' => 'cinema', 'venue' => 'salle-polyvalente-du-campus', 'days' => 6, 'time' => '18:00', 'price' => 3, 'published' => true,
            'tags' => ['etudiants'],
            'summary' => 'Cinq courts métrages de jeunes réalisateurs, suivis d\'un débat.',
            'description' => "Le ciné-club du campus projette cinq courts métrages récents, de la comédie au documentaire. La séance dure environ une heure et demie.\n\nAprès la projection, un échange est prévu avec le public sur la fabrication d'un film avec peu de moyens.",
        ],
        [
            'title' => 'Conférence : l\'olivier, de l\'arbre à l\'huile',
            'category' => 'conference', 'venue' => 'bibliotheque-regionale', 'days' => 8, 'time' => '17:00', 'price' => 0, 'published' => true,
            'tags' => ['en-famille'],
            'summary' => 'Un ingénieur agronome raconte le parcours d\'une olive, de la récolte au moulin.',
            'description' => "La région compte parmi les grandes zones oléicoles du pays. La conférence explique la taille, la récolte, le pressage et ce qui fait la qualité d'une huile.\n\nUne dégustation de plusieurs huiles de la région termine la rencontre.",
        ],
        [
            'title' => 'Concert rock : Les Dunes Électriques',
            'category' => 'concert', 'venue' => 'espace-jeunes', 'days' => 10, 'time' => '21:00', 'price' => 12, 'published' => true,
            'tags' => ['etudiants'],
            'summary' => 'Le groupe local présente son premier album sur la scène de l\'Espace jeunes.',
            'description' => "Quatre musiciens, des guitares saturées et des textes en arabe et en français. Les Dunes Électriques jouent leur premier album en entier.\n\nUn groupe de lycéens ouvre la soirée à 20 h 30.",
        ],
        [
            'title' => 'Atelier de calligraphie arabe',
            'category' => 'atelier', 'venue' => 'bibliotheque-regionale', 'days' => 12, 'time' => '14:30', 'price' => 10, 'published' => true,
            'tags' => ['debutants-bienvenus', 'sur-reservation'],
            'summary' => 'Découvrez le calame, l\'encre et les premières lettres de l\'écriture naskhi.',
            'description' => "Un calligraphe présente les outils et les proportions des lettres, puis chaque participant s'exerce sur des modèles simples.\n\nLe matériel est fourni. L'atelier convient aux adolescents à partir de 14 ans et aux adultes.",
        ],
        [
            'title' => 'Course solidaire de 10 km',
            'category' => 'sport', 'venue' => 'complexe-sportif-municipal', 'days' => 14, 'time' => '08:00', 'price' => 5, 'published' => true,
            'tags' => ['en-plein-air', 'en-famille'],
            'summary' => 'Une course ouverte à tous, en courant ou en marchant. Les inscriptions financent la bibliothèque.',
            'description' => "Le parcours de 10 km part du complexe sportif et fait une boucle par les routes de campagne. Une version de 3 km est prévue pour les enfants et les marcheurs.\n\nLes frais d'inscription servent à acheter des livres pour la bibliothèque régionale.",
        ],
        [
            'title' => 'Exposition : Regards de jeunes artistes',
            'category' => 'exposition', 'venue' => 'maison-de-la-culture', 'days' => 16, 'time' => '10:00', 'price' => 0, 'published' => true,
            'tags' => ['etudiants'],
            'summary' => 'Peintures et dessins des élèves de l\'atelier d\'arts plastiques.',
            'description' => "Une vingtaine d'élèves exposent les travaux de leur année : portraits, paysages et compositions abstraites.\n\nLe vernissage a lieu le premier jour à 17 h, en présence des artistes.",
        ],
        [
            'title' => 'Soirée jazz au jardin',
            'category' => 'concert', 'venue' => 'theatre-de-plein-air', 'days' => 19, 'time' => '20:00', 'price' => 15, 'published' => true,
            'tags' => ['en-plein-air'],
            'summary' => 'Un trio piano, contrebasse et batterie pour une soirée de standards.',
            'description' => "Le trio reprend des standards du jazz et quelques compositions inspirées des musiques du Maghreb.\n\nDes chaises sont installées sur la pelouse du théâtre. Ouverture des portes à 19 h 30.",
        ],
        [
            'title' => 'Conférence : créer son application mobile',
            'category' => 'conference', 'venue' => 'salle-polyvalente-du-campus', 'days' => 22, 'time' => '10:00', 'price' => 0, 'published' => true,
            'tags' => ['etudiants', 'sur-reservation'],
            'summary' => 'De l\'idée à la publication : les étapes pour sortir une première application.',
            'description' => "Deux développeurs racontent comment ils ont publié leur première application : choisir une idée, faire une maquette, coder, tester et publier.\n\nLa conférence s'adresse aux étudiants de toutes les filières. Les places sont limitées à quatre-vingts.",
        ],
        [
            'title' => 'Projection en plein air : film d\'animation',
            'category' => 'cinema', 'venue' => 'theatre-de-plein-air', 'days' => 27, 'time' => '19:30', 'price' => 0, 'published' => true,
            'tags' => ['en-famille', 'en-plein-air'],
            'summary' => 'Un grand écran, des coussins et un film d\'animation pour petits et grands.',
            'description' => "Le théâtre de plein air se transforme en cinéma le temps d'une soirée. Le film est projeté en version française.\n\nApportez un coussin ou une couverture. Du thé et des gâteaux sont vendus sur place.",
        ],
        [
            'title' => 'Atelier cuisine : les gâteaux du sud',
            'category' => 'atelier', 'venue' => 'espace-jeunes', 'days' => 33, 'time' => '15:00', 'price' => 20, 'published' => true,
            'tags' => ['sur-reservation', 'en-famille'],
            'summary' => 'Préparez makroudh et ghraïba avec une pâtissière de la région.',
            'description' => "Pendant deux heures, une pâtissière montre la préparation de deux gâteaux traditionnels : le makroudh aux dattes et la ghraïba.\n\nChaque participant repart avec une boîte de gâteaux. Le prix comprend les ingrédients.",
        ],
        [
            'title' => 'Tournoi d\'échecs ouvert à tous',
            'category' => 'sport', 'venue' => 'bibliotheque-regionale', 'days' => 40, 'time' => '09:30', 'price' => 0, 'published' => true,
            'tags' => ['debutants-bienvenus'],
            'summary' => 'Sept rondes en partie rapide, débutants et joueurs confirmés.',
            'description' => "Le tournoi se joue en sept rondes de quinze minutes par joueur. Les appariements tiennent compte du niveau de chacun.\n\nDes échiquiers sont prêtés sur place. Une initiation est proposée aux débutants à 9 h.",
        ],
        [
            'title' => 'Concert de fin d\'année de l\'école de musique',
            'category' => 'concert', 'venue' => 'maison-de-la-culture', 'days' => 48, 'time' => '18:30', 'price' => 5, 'published' => true,
            'tags' => ['en-famille'],
            'summary' => 'Les élèves de l\'école de musique montent sur scène pour leur concert annuel.',
            'description' => "Piano, guitare, oud et chorale : les élèves de tous les niveaux présentent ce qu'ils ont travaillé pendant l'année.\n\nLa recette du concert finance l'achat de nouveaux instruments.",
        ],
        [
            'title' => 'Concert d\'ouverture de la saison',
            'category' => 'concert', 'venue' => 'maison-de-la-culture', 'days' => -3, 'time' => '20:00', 'price' => 10, 'published' => true,
            'tags' => ['en-famille'],
            'summary' => 'La Maison de la culture a lancé sa saison avec un concert de musique tunisienne.',
            'description' => "Pour ouvrir la saison, plusieurs musiciens de la région ont partagé la scène dans un programme de chansons tunisiennes.\n\nLa salle était pleine. Le programme complet de la saison était distribué à l'entrée.",
        ],
        [
            'title' => 'Atelier photo : la lumière du matin',
            'category' => 'atelier', 'venue' => 'galerie-du-centre-ville', 'days' => -9, 'time' => '08:30', 'price' => 10, 'published' => true,
            'tags' => ['debutants-bienvenus'],
            'summary' => 'Une sortie photo au lever du jour, avec un téléphone ou un appareil.',
            'description' => "Le groupe a parcouru le centre-ville au lever du jour pour apprendre à cadrer et à jouer avec la lumière rasante.\n\nLes meilleures photos seront exposées à la galerie le mois prochain.",
        ],
        [
            'title' => 'Finale régionale de basket des jeunes',
            'category' => 'sport', 'venue' => 'complexe-sportif-municipal', 'days' => -15, 'time' => '16:00', 'price' => 0, 'published' => true,
            'tags' => ['en-famille', 'etudiants'],
            'summary' => 'Les deux meilleures équipes de moins de 18 ans se sont affrontées.',
            'description' => "La finale s'est jouée devant une salle comble. Le match s'est décidé dans les dernières secondes.\n\nLa remise des coupes a suivi, avec les familles des joueurs.",
        ],
        [
            'title' => 'Projection : documentaire sur les oasis',
            'category' => 'cinema', 'venue' => 'salle-polyvalente-du-campus', 'days' => -21, 'time' => '18:00', 'price' => 0, 'published' => true,
            'tags' => ['etudiants'],
            'summary' => 'Un documentaire sur la vie et l\'eau dans les oasis du sud tunisien.',
            'description' => "Le film suit une année dans une oasis, entre récolte des dattes et partage de l'eau.\n\nLa projection a été suivie d'un débat sur la gestion de l'eau dans la région.",
        ],
        [
            'title' => 'Conférence : l\'eau et l\'agriculture',
            'category' => 'conference', 'venue' => 'bibliotheque-regionale', 'days' => -30, 'time' => '10:00', 'price' => 0, 'published' => true,
            'tags' => ['en-famille'],
            'summary' => 'Comment irriguer mieux avec moins d\'eau : retours d\'expérience d\'agriculteurs.',
            'description' => "Des agriculteurs ont présenté leurs essais de goutte-à-goutte et de paillage pour économiser l'eau.\n\nLes supports de la conférence sont disponibles à la bibliothèque.",
        ],
        [
            'title' => 'Atelier de théâtre pour adolescents',
            'category' => 'atelier', 'venue' => 'espace-jeunes', 'days' => 20, 'time' => '14:00', 'price' => 0, 'published' => false,
            'tags' => ['debutants-bienvenus'],
            'summary' => 'Improvisation et jeux de scène pour les jeunes de 13 à 17 ans.',
            'description' => "Un comédien propose des exercices d'improvisation, de voix et de placement sur scène.\n\nL'atelier se termine par une courte présentation devant les parents.",
        ],
        [
            'title' => 'Concert de guitare classique',
            'category' => 'concert', 'venue' => 'galerie-du-centre-ville', 'days' => 25, 'time' => '19:00', 'price' => 8, 'published' => false,
            'tags' => [],
            'summary' => 'Un récital intimiste au milieu des œuvres exposées.',
            'description' => "Un guitariste joue des pièces espagnoles et latino-américaines dans la galerie.\n\nLe nombre de places est limité à cinquante.",
        ],
        [
            'title' => 'Randonnée découverte de la région',
            'category' => 'sport', 'venue' => 'complexe-sportif-municipal', 'days' => 30, 'time' => '07:30', 'price' => 5, 'published' => false,
            'tags' => ['en-plein-air'],
            'summary' => 'Douze kilomètres à pied entre collines et vergers.',
            'description' => "Le départ se fait en bus depuis le complexe sportif. La marche dure environ quatre heures avec des pauses.\n\nPrévoir de l'eau, un chapeau et de bonnes chaussures.",
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all()->keyBy('slug');
        $venues = Venue::all()->keyBy('slug');
        $tags = Tag::all();

        foreach (self::EVENTS as $data) {
            $event = Event::create([
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'summary' => $data['summary'],
                'description' => $data['description'],
                'starts_at' => today()->addDays($data['days'])->setTimeFromTimeString($data['time']),
                'price' => $data['price'],
                'is_published' => $data['published'],
                'category_id' => $categories[$data['category']]->id,
                'venue_id' => $venues[$data['venue']]->id,
            ]);

            $event->tags()->attach($tags->whereIn('slug', $data['tags'])->pluck('id'));
        }
    }
}
