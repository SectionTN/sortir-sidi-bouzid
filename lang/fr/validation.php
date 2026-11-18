<?php

return [

    'array' => 'Le champ :attribute doit être une liste.',
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'date' => 'Le champ :attribute doit être une date valide.',
    'exists' => 'La valeur choisie pour le champ :attribute n\'existe pas.',
    'integer' => 'Le champ :attribute doit être un nombre entier.',
    'max' => [
        'numeric' => 'Le champ :attribute ne doit pas dépasser :max.',
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
    ],
    'min' => [
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'not_in' => 'Ce :attribute est réservé par une page du site.',
    'regex' => 'Le format du champ :attribute n\'est pas valide.',
    'required' => 'Le champ :attribute est obligatoire.',
    'string' => 'Le champ :attribute doit être un texte.',
    'unique' => 'Ce :attribute est déjà utilisé par un autre événement.',

    'custom' => [
        'slug' => [
            'regex' => 'Le slug ne peut contenir que des minuscules sans accent, des chiffres et des tirets.',
        ],
    ],

    'attributes' => [
        'title' => 'titre',
        'slug' => 'slug',
        'summary' => 'résumé',
        'description' => 'description',
        'starts_at' => 'date et heure',
        'price' => 'prix',
        'category_id' => 'catégorie',
        'venue_id' => 'lieu',
        'tags' => 'tags',
        'tags.*' => 'tag',
        'is_published' => 'publié',
    ],

];
