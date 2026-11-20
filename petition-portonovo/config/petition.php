<?php

return [

    /*
    | Titre et objectif de la pétition, affichés sur le site et dans les e-mails.
    */
    'title' => env('PETITION_TITLE', 'Que les événements tech viennent aussi à Porto-Novo'),

    'goal_steps' => [100, 250, 500, 1000, 2500, 5000, 10000, 25000, 50000, 100000],

    /*
    | Si true, une signature n'est comptée qu'après un clic sur le lien
    | de confirmation envoyé par e-mail. Recommandé en production.
    */
    'require_verification' => (bool) env('PETITION_REQUIRE_VERIFICATION', true),

    'verification_days' => 7,

    /*
    | Accès à l'espace d'administration (authentification HTTP Basic).
    | Laisser ADMIN_PASSWORD vide désactive l'administration.
    */
    'admin_user' => env('PETITION_ADMIN_USER', 'admin'),
    'admin_password' => env('PETITION_ADMIN_PASSWORD'),

    'profiles' => [
        'etudiant' => 'Étudiant(e)',
        'developpeur' => 'Développeur(se)',
        'entrepreneur' => 'Entrepreneur(e)',
        'creatif' => 'Créatif / Designer',
        'organisateur' => 'Organisateur d’événements',
        'enseignant' => 'Enseignant(e) / Formateur(trice)',
        'citoyen' => 'Citoyen(ne)',
        'autre' => 'Autre',
    ],

    'hashtags' => ['PortoNovo', 'BeninNumerique', 'TechBenin', 'Innovation', 'JeunesseBeninoise', 'Decentralisation', 'Numerique', 'Hackathon'],
];
