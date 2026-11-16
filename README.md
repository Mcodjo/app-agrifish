🌍 AgriFish — La plateforme digitale intelligente pour l’agriculture et l’aquaculture en Afrique

AgriFish est une application web professionnelle qui centralise l’accompagnement agricole, la gestion de projets, les services techniques et la formation digitale au sein d’un seul écosystème intelligent.

Conçue pour les producteurs, investisseurs, coopératives et porteurs de projets, AgriFish transforme le conseil agricole traditionnel en une plateforme moderne, structurée et scalable.

🚀 Une solution complète en un seul outil

Avec AgriFish, les utilisateurs peuvent :

✅ Accéder à des services d’expertise agricole et piscicole
✅ Suivre leurs projets en temps réel
✅ Gérer documents, rapports et études techniques
✅ Communiquer directement avec des experts
✅ Payer leurs prestations en ligne en toute sécurité
✅ Se former via des modules digitaux certifiants

🧠 Une plateforme structurée autour de 3 pôles stratégiques

1. Site institutionnel premium
Pour présenter l’impact, les services, les réalisations et renforcer la crédibilité auprès des partenaires et investisseurs.

2. Espace client intelligent
Un tableau de bord personnalisé pour chaque utilisateur avec suivi de projet, documents, messagerie, demandes de services et paiements intégrés.

3. Pôle formation digitale
Un centre de formation en ligne avec vidéos, supports PDF, inscriptions, paiements et certificats téléchargeables.

🌱 Pensée pour l’Afrique, conçue pour la performance

AgriFish est optimisée pour :

• Connexions faibles
• Utilisation mobile
• Paiements locaux
• Simplicité d’usage terrain

🎯 Objectif

Digitaliser l’accompagnement agricole, professionnaliser les services ruraux et accélérer la rentabilité des projets agricoles et piscicoles en Afrique.

💡 En résumé

AgriFish n’est pas un simple site web.
C’est une plateforme digitale complète qui structure, automatise et développe l’agriculture moderne.

---

## 📍 État d’avancement

| Fonctionnalité | État |
|---|---|
| Site institutionnel (accueil, services, projets, formation, contact) | ✅ Disponible |
| Espace client : demandes, suivi de jalons, documents, messagerie | ✅ Disponible |
| Espace expert (projets assignés, jalons, livrables) et admin (assignation, statuts) | ✅ Disponible |
| Paiement en ligne | ⏳ Pas encore développé |
| Pôle formation digitale (vidéos, inscriptions, certificats) | ⏳ Pas encore développé (page vitrine uniquement) |

## 🛠️ Installation (développement)

Prérequis : PHP 8.4+, Composer, Node 20+.

```bash
composer setup          # installe, crée .env, génère la clé, migre et compile les assets
php artisan db:seed     # comptes de démo (environnement local uniquement)
composer dev            # serveur, file d’attente, logs et Vite
composer test           # tests
```

## 🔐 Sécurité

- Les fichiers clients (documents, livrables, pièces jointes) sont stockés sur le disque **privé**
  `storage/app/private` et ne sont servis que par des routes contrôlées par `App\Policies\ServiceRequestPolicy`.
- Un expert n’accède qu’aux projets qui lui sont assignés ; un client qu’à ses propres projets.
- Le rôle (`client`, `expert`, `admin`) n’est pas modifiable par formulaire ; il s’attribue avec `forceFill`, par exemple :
  `php artisan tinker` puis `User::where('email', '…')->first()->forceFill(['role' => 'admin'])->save();`
- En production : `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, et ne jamais lancer les seeders de démo.
