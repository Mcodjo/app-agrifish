# Pétition Tech Porto-Novo

Site de pétition en ligne : **« Que les événements tech viennent aussi à Porto-Novo »**.
Plaidoyer de Maurice Michel Hotegni CODJO pour la décentralisation des événements tech et de l’innovation au Bénin.

Application Laravel 12 autonome (PHP 8.2+, SQLite ou MySQL). Aucune compilation front-end n’est nécessaire : la feuille de style est servie depuis `public/css/petition.css`.

## Fonctionnalités

- Page de la pétition avec le texte complet du plaidoyer, compteur de signatures et barre de progression vers le prochain palier (100, 250, 500, 1 000…).
- Signature sans création de compte : prénom, nom, e-mail, commune, profil, mot facultatif pour les décideurs.
- **Confirmation par e-mail** : une signature n’est comptée qu’après un clic sur le lien reçu (lien signé, valable 7 jours). Désactivable avec `PETITION_REQUIRE_VERIFICATION=false`.
- Une seule signature par adresse e-mail.
- Protection anti-spam : champ piège invisible pour les robots, limite de 6 envois par minute et par adresse IP, au plus un e-mail de confirmation toutes les 10 minutes par adresse.
- Confidentialité : l’e-mail n’est jamais publié ; le public voit uniquement « Prénom N. », la commune et le profil, ou « Signataire anonyme » si la personne le choisit. L’adresse IP n’est conservée que sous forme d’empreinte (hash).
- Liste publique des signataires (`/signataires`) et classement des communes les plus mobilisées.
- Boutons de partage WhatsApp, Facebook, LinkedIn, X et copie du message avec les hashtags.
- Espace d’administration (`/admin`, protégé par mot de passe) : statistiques, répartition par profil et par commune, recherche, suppression de signatures abusives, **export CSV** (compatible Excel) à remettre aux autorités.

## Installation locale

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

Le site est alors disponible sur http://127.0.0.1:8000.

En local, `MAIL_MAILER=log` écrit les e-mails de confirmation dans `storage/logs/laravel.log` : copiez-y le lien pour confirmer une signature de test.

## Configuration (`.env`)

| Variable | Rôle |
| --- | --- |
| `APP_URL` | Adresse publique du site (utilisée dans les liens de partage et de confirmation). |
| `PETITION_REQUIRE_VERIFICATION` | `true` pour exiger la confirmation par e-mail (recommandé). |
| `PETITION_ADMIN_USER` / `PETITION_ADMIN_PASSWORD` | Identifiants de `/admin`. Sans mot de passe, l’administration est désactivée. |
| `MAIL_*` | Serveur d’envoi des e-mails (Brevo, Mailgun, Gmail SMTP…). |

Le titre, les profils proposés, les paliers et les hashtags se modifient dans `config/petition.php`.

## Mise en production

1. Hébergement PHP 8.2+ (o2switch, Hostinger, LWS, un VPS…) avec le dossier `public/` comme racine web.
2. `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://votre-domaine`.
3. Configurer un vrai service d’envoi d’e-mails dans `MAIL_*` et une adresse `MAIL_FROM_ADDRESS` du domaine.
4. Choisir un mot de passe d’administration solide dans `PETITION_ADMIN_PASSWORD`.
5. `composer install --no-dev --optimize-autoloader`, `php artisan migrate --force`, `php artisan config:cache`, `php artisan route:cache`.
6. Activer HTTPS.

## Tests

```bash
php artisan test
```
