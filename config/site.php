<?php

/*
| Contenu éditorial du site vitrine.
|
| Les coordonnées se règlent dans le fichier .env. Une valeur vide masque
| l'élément correspondant (téléphone, bouton WhatsApp).
| Les chiffres et les réalisations doivent être vérifiés avant la mise en ligne.
*/

return [

    'contact' => [
        'email' => env('SITE_EMAIL') ?: 'contact@agrifish.africa',
        'telephone' => env('SITE_TELEPHONE') ?: null,
        // Numéro au format international, chiffres uniquement (ex. 2250700000000).
        'whatsapp' => env('SITE_WHATSAPP') ?: null,
        'zone' => 'Afrique de l’Ouest',
        'delai' => '24 heures ouvrées',
    ],

    // Mentions légales. Une ligne vide n'est pas affichée sur le site.
    'legal' => [
        'raison_sociale' => env('LEGAL_RAISON_SOCIALE') ?: 'AgriFish',
        'forme' => env('LEGAL_FORME') ?: null,
        'capital' => env('LEGAL_CAPITAL') ?: null,
        'siege' => env('LEGAL_SIEGE') ?: null,
        'immatriculation' => env('LEGAL_IMMATRICULATION') ?: null,
        'identifiant_fiscal' => env('LEGAL_IDENTIFIANT_FISCAL') ?: null,
        'directeur_publication' => env('LEGAL_DIRECTEUR_PUBLICATION') ?: null,
        'email_donnees' => env('LEGAL_EMAIL_DONNEES') ?: (env('SITE_EMAIL') ?: 'contact@agrifish.africa'),
        'hebergeur' => [
            'nom' => env('HEBERGEUR_NOM') ?: null,
            'adresse' => env('HEBERGEUR_ADRESSE') ?: null,
            'site' => env('HEBERGEUR_SITE') ?: null,
            'telephone' => env('HEBERGEUR_TELEPHONE') ?: null,
        ],
        'mise_a_jour' => env('LEGAL_MISE_A_JOUR') ?: '22 novembre 2026',
    ],

    'chiffres' => [
        ['valeur' => '12', 'libelle' => 'pays d’intervention'],
        ['valeur' => '120+', 'libelle' => 'producteurs accompagnés'],
        ['valeur' => '3', 'libelle' => 'domaines d’expertise'],
        ['valeur' => '24 h', 'libelle' => 'pour une première réponse'],
    ],

    'services' => [
        'agriculture' => [
            'titre' => 'Agriculture',
            'intitule' => 'Conseil agricole',
            'resume' => 'Diagnostic de parcelle, itinéraires techniques et suivi des cultures, pour une exploitation familiale comme pour un projet de plus grande taille.',
            'photo' => 'maraichage',
            'photo_alt' => 'Deux producteurs arrosent et entretiennent une parcelle de légumes.',
            'prestations' => [
                ['Diagnostic de terrain', 'Visite des parcelles, état du sol, accès à l’eau et pratiques en place.'],
                ['Plan cultural', 'Choix des cultures, calendrier et itinéraire technique adaptés à votre zone.'],
                ['Suivi technique', 'Passages réguliers, fiches de suivi et ajustements en cours de campagne.'],
                ['Recommandations', 'Un rapport qui hiérarchise les actions, avec leur coût et le gain attendu.'],
            ],
        ],
        'aquaculture' => [
            'titre' => 'Aquaculture',
            'intitule' => 'Développement piscicole',
            'resume' => 'Conception de sites d’élevage, gestion de l’eau et appui technique au démarrage de la production.',
            'photo' => 'etangs-piscicoles',
            'photo_alt' => 'Des pisciculteurs nourrissent les poissons au bord de deux étangs.',
            'prestations' => [
                ['Choix du site', 'Qualité et débit de l’eau, nature du sol et accès, avant d’implanter les bassins.'],
                ['Dimensionnement', 'Nombre et taille des bassins ou étangs, équipements et budget d’installation.'],
                ['Gestion de l’eau', 'Paramètres à surveiller, fréquence des mesures et gestes correctifs.'],
                ['Rendement', 'Alimentation, densités et calendrier de récolte pour améliorer la production.'],
            ],
        ],
        'etudes' => [
            'titre' => 'Études techniques',
            'intitule' => 'Études de faisabilité',
            'resume' => 'Pré-études, chiffrage et scénarios d’investissement, pour décider avant d’engager les fonds.',
            'photo' => 'suivi-pepiniere',
            'photo_alt' => 'Une technicienne saisit des données sur un ordinateur portable dans une pépinière.',
            'prestations' => [
                ['Faisabilité', 'Analyse technique du projet, contraintes du site et conditions de réussite.'],
                ['Budget prévisionnel', 'Coûts d’installation, charges d’exploitation et besoin de financement.'],
                ['Analyse des risques', 'Risques techniques, climatiques et commerciaux, et moyens de les limiter.'],
                ['Feuille de route', 'Étapes, calendrier et indicateurs pour lancer le projet.'],
            ],
        ],
    ],

    'methode' => [
        ['Premier échange', 'Vous décrivez votre projet par téléphone, par message ou depuis votre espace client. Nous précisons ensemble le besoin et le périmètre de la mission.'],
        ['Diagnostic', 'Visite de terrain ou analyse des documents existants, selon la mission. Nous relevons les contraintes et les points à traiter en priorité.'],
        ['Préconisations', 'Vous recevez un livrable écrit et une feuille de route chiffrée, présentés lors d’un échange avec l’expert.'],
        ['Suivi', 'Les jalons, les documents et les échanges restent accessibles dans votre espace client pendant toute la durée du projet.'],
    ],

    'publics' => [
        ['Producteurs', 'Exploitations familiales et producteurs qui veulent améliorer leurs rendements ou démarrer une nouvelle activité.'],
        ['Coopératives et groupements', 'Organisation de la production, formation des membres et préparation des projets collectifs.'],
        ['Entreprises et investisseurs', 'Études préalables, chiffrage et suivi technique de projets agricoles ou piscicoles.'],
        ['ONG et projets de développement', 'Appui technique, formation des bénéficiaires et suivi des activités sur le terrain.'],
    ],

    'realisations' => [
        ['secteur' => 'Agriculture', 'titre' => 'Coopérative maraîchère', 'description' => 'Diagnostic des parcelles, planification des cultures et formation des membres.', 'lieu' => 'Kindia, Guinée'],
        ['secteur' => 'Aquaculture', 'titre' => 'Unité piscicole pilote', 'description' => 'Étude technique du site, puis appui au lancement de la production.', 'lieu' => 'Abidjan, Côte d’Ivoire'],
        ['secteur' => 'Études', 'titre' => 'Faisabilité d’un site mixte', 'description' => 'Comparaison de scénarios techniques et financiers pour un site agricole et piscicole.', 'lieu' => 'Bamako, Mali'],
        ['secteur' => 'Formation', 'titre' => 'Atelier de terrain', 'description' => 'Sessions pratiques pour les opérateurs et leurs encadrants.', 'lieu' => 'Dakar, Sénégal'],
        ['secteur' => 'Conseil', 'titre' => 'Structuration de projet', 'description' => 'Appui au pilotage du projet et à la préparation des décisions.', 'lieu' => 'Accra, Ghana'],
        ['secteur' => 'Suivi', 'titre' => 'Tableau de bord de projet', 'description' => 'Mise en place du suivi des jalons et capitalisation des résultats.', 'lieu' => 'Ouagadougou, Burkina Faso'],
    ],

    'formations' => [
        ['categorie' => 'Agriculture', 'titre' => 'Maraîchage tropical intensif', 'description' => 'Variétés adaptées, irrigation et protection des cultures en climat tropical.', 'modules' => 6, 'duree' => '4 h 30', 'niveau' => 'Débutant'],
        ['categorie' => 'Aquaculture', 'titre' => 'Pisciculture en bassin', 'description' => 'Créer et conduire une ferme piscicole, du choix du site à la récolte.', 'modules' => 8, 'duree' => '6 h', 'niveau' => 'Débutant'],
        ['categorie' => 'Agriculture', 'titre' => 'Gestion de l’irrigation', 'description' => 'Choisir et entretenir un système d’irrigation pour une petite ou moyenne exploitation.', 'modules' => 5, 'duree' => '3 h 15', 'niveau' => 'Intermédiaire'],
        ['categorie' => 'Gestion', 'titre' => 'Gestion financière agricole', 'description' => 'Tenir sa comptabilité, calculer sa rentabilité et piloter son exploitation.', 'modules' => 7, 'duree' => '5 h', 'niveau' => 'Intermédiaire'],
        ['categorie' => 'Aquaculture', 'titre' => 'Crevetticulture en eau douce', 'description' => 'Les bases de l’élevage de crevettes d’eau douce pour les marchés locaux.', 'modules' => 6, 'duree' => '4 h', 'niveau' => 'Avancé'],
        ['categorie' => 'Gestion', 'titre' => 'Plan d’affaires agricole', 'description' => 'Rédiger un plan d’affaires solide pour présenter son projet à un financeur.', 'modules' => 5, 'duree' => '3 h 30', 'niveau' => 'Tous niveaux'],
    ],

    'faq' => [
        ['Dans quelles zones intervenez-vous ?', 'Principalement en Afrique de l’Ouest. Pour un projet situé ailleurs, écrivez-nous : nous étudions chaque demande.'],
        ['Travaillez-vous avec les petits producteurs ?', 'Oui. Nous intervenons pour un producteur individuel comme pour une coopérative, une entreprise ou un projet financé par un bailleur. Le format de la mission est adapté à chaque cas.'],
        ['Comment se déroule une mission ?', 'Après un premier échange, nous réalisons le diagnostic, puis nous vous remettons nos préconisations et une feuille de route. Le suivi se poursuit selon ce qui a été convenu.'],
        ['Comment suivre l’avancement de mon projet ?', 'Chaque client dispose d’un espace en ligne. Il y retrouve les étapes du projet, les documents, les livrables et la messagerie avec son expert.'],
        ['Combien coûte un accompagnement ?', 'Le prix dépend de la mission, de la taille du projet et des déplacements nécessaires. Après le premier échange, nous vous adressons un devis détaillé.'],
        ['Sous quel délai répondez-vous ?', 'Nous répondons sous 24 heures ouvrées aux demandes envoyées par le formulaire de contact ou depuis l’espace client.'],
    ],

    'faq_formation' => [
        ['Pouvez-vous organiser une session pour un groupe ?', 'Oui. Pour une coopérative, une entreprise ou un projet, nous organisons des sessions dédiées. Indiquez le nombre de participants et le lieu souhaité dans votre demande.'],
        ['Faut-il un niveau particulier ?', 'Le niveau conseillé est indiqué sur chaque formation. Les formations de niveau débutant ne demandent aucun prérequis.'],
        ['Comment s’inscrire ?', 'Envoyez votre demande depuis la page contact ou votre espace client. Nous revenons vers vous avec les dates, le lieu et les conditions.'],
    ],

    // Photographies d'illustration : à remplacer par des photos de vos missions dès que possible.
    'photos' => [
        'conseil-terrain' => ['titre' => 'CTA Pejeriz project field visit, Senegal', 'auteur' => 'CTA-EU', 'licence' => 'CC BY-SA 2.0', 'licence_url' => 'https://creativecommons.org/licenses/by-sa/2.0/', 'source' => 'https://www.flickr.com/photos/89728624@N04/48413509206'],
        'suivi-pepiniere' => ['titre' => 'CTA Pejeriz project field visit, Senegal', 'auteur' => 'CTA-EU', 'licence' => 'CC BY-SA 2.0', 'licence_url' => 'https://creativecommons.org/licenses/by-sa/2.0/', 'source' => 'https://www.flickr.com/photos/89728624@N04/48413660702'],
        'maraichage' => ['titre' => 'FTF RESINA', 'auteur' => 'USAID Mozambique', 'licence' => 'Domaine public', 'licence_url' => 'https://creativecommons.org/publicdomain/mark/1.0/', 'source' => 'https://www.flickr.com/photos/62256332@N08/54339877883'],
        'etangs-piscicoles' => ['titre' => 'FTF RESINA', 'auteur' => 'USAID Mozambique', 'licence' => 'Domaine public', 'licence_url' => 'https://creativecommons.org/publicdomain/mark/1.0/', 'source' => 'https://www.flickr.com/photos/62256332@N08/54340083880'],
        'producteurs-tomates' => ['titre' => 'FTF RESINA', 'auteur' => 'USAID Mozambique', 'licence' => 'Domaine public', 'licence_url' => 'https://creativecommons.org/publicdomain/mark/1.0/', 'source' => 'https://www.flickr.com/photos/62256332@N08/54339654091'],
        'formation-producteurs' => ['titre' => 'Training women farmers on climate smart innovations in Nyando, Kenya', 'auteur' => 'CGIAR Climate', 'licence' => 'CC BY 2.0', 'licence_url' => 'https://creativecommons.org/licenses/by/2.0/', 'source' => 'https://www.flickr.com/photos/55227776@N04/9417196732'],
        // Photos des produits de démonstration de la boutique.
        'boutique-aliment-granules' => ['titre' => 'Automatic fish feeder', 'auteur' => 'USFWS Mountain Prairie', 'licence' => 'Domaine public', 'licence_url' => 'https://creativecommons.org/publicdomain/mark/1.0/', 'source' => 'https://www.flickr.com/photos/51986662@N05/49623831351'],
        'boutique-compost' => ['titre' => '20240730-USDA-RD-CO-JJP-Table to Farm Compost, Durango-0011', 'auteur' => 'USDA Rural Development', 'licence' => 'Domaine public', 'licence_url' => 'https://creativecommons.org/publicdomain/mark/1.0/', 'source' => 'https://www.flickr.com/photos/144445608@N03/53952552476'],
        'boutique-goutte-a-goutte' => ['titre' => 'installing-drip-irrigation-in-a-high-tunnel_9662780905_o', 'auteur' => 'USDA NRCS New Mexico', 'licence' => 'Domaine public', 'licence_url' => 'https://creativecommons.org/publicdomain/mark/1.0/', 'source' => 'https://www.flickr.com/photos/98949540@N04/49030250418'],
        'boutique-semences-maraicheres' => ['titre' => 'Vegetable seeds sprout grow lights', 'auteur' => 'U.S. Department of Agriculture', 'licence' => 'CC0 1.0', 'licence_url' => 'https://creativecommons.org/publicdomain/zero/1.0/', 'source' => 'https://www.rawpixel.com/image/8731958/photo-image-plant-public-domain-soil'],
        'boutique-mais-semences' => ['titre' => 'Corn', 'auteur' => 'Thad Zajdowicz', 'licence' => 'CC0 1.0', 'licence_url' => 'https://creativecommons.org/publicdomain/zero/1.0/', 'source' => 'https://www.flickr.com/photos/40632439@N00/6273685781'],
        'boutique-filet-peche' => ['titre' => 'Fishing Nets', 'auteur' => 'Mustang Joe', 'licence' => 'CC0 1.0', 'licence_url' => 'https://creativecommons.org/publicdomain/zero/1.0/', 'source' => 'https://www.flickr.com/photos/63234672@N04/40175944233'],
        'boutique-flotteurs' => ['titre' => 'Fishing net floats.Icy Strait Point Alaska.', 'auteur' => 'Bernard Spragg', 'licence' => 'CC0 1.0', 'licence_url' => 'https://creativecommons.org/publicdomain/zero/1.0/', 'source' => 'https://www.flickr.com/photos/88123769@N02/12450903104'],
        'boutique-arrosoir' => ['titre' => 'watering can', 'auteur' => 'brenkee', 'licence' => 'CC0 1.0', 'licence_url' => 'https://creativecommons.org/publicdomain/zero/1.0/', 'source' => 'https://www.flickr.com/photos/55128416@N05/14811313052'],
    ],

];
