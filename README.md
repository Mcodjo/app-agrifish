# AgriFish

Site et espace client d’AgriFish, cabinet de conseil technique pour les projets agricoles et aquacoles en Afrique de l’Ouest : conseil, études de faisabilité, formation et suivi de projet en ligne.

Application Laravel 12 (PHP 8.4), Blade, Tailwind CSS et Alpine.js.

## Fonctionnalités

| Domaine | Contenu |
|---|---|
| Site public | Accueil, services, formation, réalisations, à propos, contact, mentions légales, politique de confidentialité |
| Espace client | Dépôt d’une demande avec documents, suivi des étapes et de l’avancement, livrables, messagerie avec l’expert, e-mail à chaque changement de statut |
| Espace expert | Projets assignés, création et mise à jour des étapes, dépôt des livrables |
| Administration | Recherche et filtres par statut, assignation d’un expert, changement de statut avec note au client |

Pas encore développé : le paiement en ligne et la formation en ligne (vidéos, inscription et attestation en ligne). La page Formation présente le catalogue et renvoie vers une demande d’inscription.

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

`composer setup` installe les dépendances, crée le fichier `.env`, génère la clé, lance les migrations et compile les assets. `db:seed` crée des comptes et des projets de démonstration (environnement local uniquement).

## Contenu du site

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
- Le rôle (`client`, `expert`, `admin`) ne peut pas être modifié par formulaire. Pour nommer un administrateur : `php artisan tinker`, puis `User::where('email', '…')->first()->forceFill(['role' => 'admin'])->save();`.
- En production : `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, et ne pas lancer les données de démonstration.

## Tests

```bash
composer test
```
