# AgriFish

Site et espace client d’AgriFish, cabinet de conseil technique pour les projets agricoles et aquacoles en Afrique de l’Ouest : conseil, études de faisabilité, formation et suivi de projet en ligne.

Application Laravel 12 (PHP 8.4), Blade, Tailwind CSS et Alpine.js.

## Fonctionnalités

| Domaine | Contenu |
|---|---|
| Site public | Accueil, services, formation, réalisations, à propos, contact, mentions légales, politique de confidentialité, conditions générales de vente |
| Boutique | Catalogue avec catégories sur deux niveaux, filtres (prix, disponibilité), recherche, tri ; fiche produit avec photos, déclinaisons, prix barrés, fiche technique, avis, bouton WhatsApp ; panier conservé 30 jours ; codes promo ; commande sans compte ; paiement Mobile Money et carte (FedaPay) ou à la remise ; facture PDF ; suivi de commande par lien privé |
| Espace client | Commandes, factures, « recommander », adresses enregistrées, avis sur les produits reçus. Dépôt d’une demande avec documents, ajout ou retrait de documents tant que la demande n’est pas engagée, annulation, suivi des étapes et de l’avancement, livrables, messagerie avec l’expert, historique de la demande, filtres et recherche |
| Espace expert | Projets assignés, création, mise à jour, réordonnancement et suppression des étapes, dépôt et retrait des livrables |
| Gestion de la boutique (administrateur et gestionnaire) | Tableau de bord (ventes du jour et du mois, panier moyen, 14 derniers jours, meilleures ventes, stock bas), commandes (préparation, prête, remise, encaissement, annulation, remboursement, notes internes), produits et déclinaisons, photos compressées en WebP, mouvements de stock tracés, catégories, codes promo, modération des avis, import et export Excel du catalogue, export des ventes |
| Administration | Réglages de la boutique (ouverture, moyens de paiement, délai de paiement, message de remise, SMS), indicateurs (demandes du mois, sans expert, délai moyen de prise en charge, répartition par statut, charge des experts), traitement des demandes, gestion des comptes (création avec invitation, changement de rôle, suspension, lien de réinitialisation) |
| E-mails | Nouveau message (au client, à l’expert, ou aux administrateurs si aucun expert n’est assigné), projet assigné à l’expert, nouvelle demande et annulation à l’équipe, changement de statut au client. Chaque personne règle ses e-mails dans son profil. |

Rôles : client, expert, gestionnaire boutique, administrateur. Les administrateurs et gestionnaires se connectent avec un code à 6 chiffres reçu par e-mail en plus du mot de passe.

La livraison n’est pas gérée par la plateforme : l’équipe organise la remise des commandes avec le client. Pas encore développé : la formation en ligne (vidéos, inscription et attestation en ligne).

## Boutique : règles de fonctionnement

- **Stock** : les articles sont réservés dès la validation de la commande, sous verrou, si bien que deux clients ne peuvent pas acheter le même dernier article. Une commande payable en ligne et non payée dans le délai (24 heures par défaut, réglable) est annulée et son stock libéré. Chaque mouvement de stock est tracé.
- **Paiement en ligne** : une commande n’est marquée payée qu’après avoir relu le statut de la transaction auprès de FedaPay ; ni le retour du navigateur ni le contenu d’une notification ne suffisent.
- **Tâches planifiées** : ajoutez au serveur la tâche cron `* * * * * cd /chemin/du/site && php artisan schedule:run`. Elle vérifie les paiements en attente toutes les 5 minutes et annule les commandes expirées toutes les 10 minutes.
- **Mise en service du paiement** : dans `.env`, `SHOP_PAYMENT_DRIVER=fedapay`, `FEDAPAY_ENVIRONMENT`, `FEDAPAY_SECRET_KEY` et `FEDAPAY_WEBHOOK_SECRET`, puis déclarez dans FedaPay l’adresse de notification `https://votre-domaine/webhooks/paiement/fedapay`. Faites d’abord une commande réelle en mode sandbox pour valider l’intégration. Sans configuration, le paiement en ligne n’est pas proposé en production.
- **Photos et fiches techniques** : stockées sur le disque public ; lancez `php artisan storage:link` à l’installation.

## Installation

Prérequis : PHP 8.4, Composer, Node 20 ou plus.

```bash
composer setup
```

```bash
php artisan db:seed
```

```bash
composer dev
```

`composer setup` installe les dépendances, crée le fichier `.env`, génère la clé, lance les migrations et compile les assets. `db:seed` crée des comptes, des projets, un catalogue et des commandes de démonstration (environnement local uniquement). Comptes : client@, expert@, boutique@ (gestionnaire) et admin@agrifish.africa, mot de passe `DEMO_USER_PASSWORD` ou `Password123!`. En local, les e-mails (dont les codes de connexion) sont écrits dans `storage/logs/laravel.log`.

## Contenu du site

- **Mentions légales** : variables `LEGAL_*` et `HEBERGEUR_*` dans `.env` (forme juridique, siège, RCCM, directeur de la publication, hébergeur…). Une ligne vide n’est pas affichée ; renseignez-les avant la mise en ligne.
- **Coordonnées** : `SITE_EMAIL`, `SITE_TELEPHONE` et `SITE_WHATSAPP` dans `.env`. Un champ vide masque l’élément (le bouton WhatsApp n’apparaît que si le numéro est renseigné).
- **Textes** : `config/site.php` regroupe les chiffres clés, les services, la méthode, les réalisations, les formations et les questions fréquentes. Les chiffres et les réalisations sont à vérifier avant la mise en ligne.
- **Photos** : `public/images/photos/`. Les images actuelles sont des illustrations sous licence libre, créditées dans `config/site.php` et sur la page Mentions légales. Pour les remplacer par des photos de vos missions, gardez le même nom de fichier en WebP, en 1024 px de large et en 640 px (suffixe `-640`).
- **Capture de l’espace client** : `public/images/plateforme/suivi-projet.webp`, affichée sur la page d’accueil.

## Charte graphique

- Titres en Newsreader, texte en Figtree. Les deux polices sont hébergées avec le site (paquets `@fontsource`), sans appel à un service externe.
- Couleurs principales : vert `#1b4332`, fond sable `#f4f2ec`, texte `#17211b`, or `#b8862b` réservé au logo et aux numéros d’étapes.
- Composants Blade réutilisables dans `resources/views/components` : `page-header`, `photo`, `steps`, `faq`, `cta-final`, `legal-page`, `status-badge`, `file-input`.

Les pages suivent la structure d’une page d’atterrissage : accroche, chiffres clés, offre, méthode en étapes, preuve (espace client, exemples de missions), questions fréquentes, appel à l’action final et pied de page.

## Sécurité

- Les documents, livrables et pièces jointes sont stockés dans `storage/app/private` et ne sont servis qu’après contrôle d’accès (`App\Policies\ServiceRequestPolicy`).
- Un expert n’accède qu’aux projets qui lui sont assignés, un client qu’à ses propres projets.
- Le rôle (`client`, `expert`, `admin`) ne peut pas être modifié par le profil. Les administrateurs gèrent les rôles depuis la page Utilisateurs. Pour nommer le tout premier administrateur : `php artisan tinker`, puis `User::where('email', '…')->first()->forceFill(['role' => 'admin'])->save();`.
- Un compte suspendu est déconnecté immédiatement et ne peut plus se connecter.
- Le formulaire « Mot de passe oublié » répond de la même façon que l’adresse existe ou non.
- Les e-mails partent au moment de l’action (pas de file d’attente à faire tourner) ; en production, renseignez `MAIL_MAILER` et les identifiants SMTP.
- En production : `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, et ne pas lancer les données de démonstration.

## Tests

```bash
composer test
```
