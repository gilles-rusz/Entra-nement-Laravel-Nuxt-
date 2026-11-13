<?php

// Messages d'erreur de validation en français (seulement les règles utilisées dans le projet).
return [
    'array' => 'Le champ :attribute doit être une liste.',
    'email' => 'Le champ :attribute doit être une adresse email valide.',
    'exists' => 'Le champ :attribute sélectionné n\'existe pas.',
    'file' => 'Le champ :attribute doit être un fichier.',
    'in' => 'La valeur du champ :attribute n\'est pas valide.',
    'integer' => 'Le champ :attribute doit être un nombre entier.',
    'max' => [
        'numeric' => 'Le champ :attribute ne doit pas dépasser :max.',
        'file' => 'Le fichier :attribute ne doit pas dépasser :max Ko.',
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        'array' => 'Le champ :attribute ne doit pas contenir plus de :max éléments.',
    ],
    'mimes' => 'Le fichier :attribute doit être de type : :values.',
    'min' => [
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'file' => 'Le fichier :attribute doit faire au moins :min Ko.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'array' => 'Le champ :attribute doit contenir au moins :min élément(s).',
    ],
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'regex' => 'Le format du champ :attribute n\'est pas valide.',
    'required' => 'Le champ :attribute est obligatoire.',
    'string' => 'Le champ :attribute doit être un texte.',

    'attributes' => [
        'password' => 'mot de passe',
        'reference' => 'référence',
        'prix_ht' => 'prix HT',
        'modele' => 'modèle',
        'lignes' => 'lignes de commande',
    ],
];
