# Structure du site v2 — AgriFish
> Refonte complète | Ton : Professionnel & institutionnel | Objectif : Génération de leads
> Stack : Laravel + Blade + Tailwind CSS + Alpine.js
> Date : 01/03/2026

---

## 🗂️ Arborescence repensée

```
AgriFish
├── /                        Accueil
├── /about                   À propos & Impact
├── /services                Nos expertises
│   ├── /services/agriculture      Pôle Agriculture
│   └── /services/aquaculture      Pôle Aquaculture
├── /formations              Formations professionnelles
├── /etudes-de-cas           Études de cas & Références  ← NOUVELLE PAGE
├── /contact                 Prendre contact
├── /mentions-legales        Mentions légales
├── /politique-confidentialite  Politique de confidentialité
└── /404                     Page introuvable
```

**Total : 10 pages | 2 pages stratégiques nouvelles**

---

## 💡 Logique de refonte

| Ancien | Nouveau | Pourquoi |
|--------|---------|----------|
| Hero générique | Hero orienté résultats | Le prospect veut voir ce qu'il va gagner |
| Services listés | Services sous-pages dédiées | Meilleur SEO + qualification du lead |
| Pas de preuves | Page Études de cas | Crédibilise auprès des institutions et bailleurs |
| CTA uniques | CTA multi-contexte | Adapter l'appel selon la maturité du prospect |
| Timeline About | Chiffres d'impact | Plus convaincant que l'histoire pour les institutionnels |

---

## 📄 Pages & Copywriting détaillé

---

### 1. ACCUEIL — `/`

#### `HeroSection` — Fond : dégradé vert profond (#1B4332 → #2D6A4F)

**Badge :** `Conseil · Formation · Innovation agricole`

**H1 :**
> "Votre partenaire stratégique pour une agriculture africaine performante"

**Accroche :**
> AgriFish accompagne les acteurs du secteur agricole et aquacole à travers des solutions de conseil sur mesure, des formations certifiantes et un suivi terrain rigoureux — dans 12 pays d'Afrique subsaharienne.

**CTA principal :** `Discuter de votre projet →` (bouton doré #C9973A)
**CTA secondaire :** `Découvrir nos expertises` (bordure blanche, fond transparent)

**3 piliers (cartes flottantes desktop) :**
- 🌱 **Conseil agricole** — Diagnostic, stratégie et accompagnement terrain
- 🎓 **Formations certifiantes** — 25+ modules accessibles sur mobile
- 🐟 **Pôle aquaculture** — De la conception à la commercialisation

**Décor :** Vague SVG bas de section, particules vertes légères

---

#### `TrustBarSection` — Fond blanc, bordure top/bottom

> Texte intro : *"Ils nous font confiance"*

5 logos partenaires (placeholder) + chiffre central :
**"150+ projets livrés dans 12 pays"**

---

#### `ImpactSection` — Fond #F5F0E8 (crème institutionnel)

**Titre :**
> "Un impact mesurable sur le terrain"

**Sous-titre :**
> Nos interventions s'appuient sur des méthodes éprouvées et des indicateurs de performance clairs, co-construits avec nos clients.

**4 métriques (grille 2×2, grandes typographies) :**

| Chiffre | Label | Sous-label |
|---------|-------|------------|
| **150+** | Projets accompagnés | Agriculture & aquaculture |
| **12** | Pays d'intervention | Afrique subsaharienne |
| **50+** | Experts mobilisables | Agronomes, aquaculteurs, formateurs |
| **2 000+** | Professionnels formés | Producteurs, techniciens, managers |

---

#### `ExpertisesSection` — Fond blanc

**Titre :**
> "Trois pôles d'expertise complémentaires"

**Sous-titre :**
> AgriFish intervient à chaque étape du développement de votre activité agricole ou aquacole.

**3 cartes (hover : élévation + bordure verte gauche) :**

**🌱 Conseil & accompagnement agricole**
> Du diagnostic initial à la mise en œuvre opérationnelle, nous co-construisons votre stratégie agricole avec vos équipes.
> `→ Voir le pôle agriculture`

**🐟 Expertise aquacole**
> Création de sites, suivi sanitaire, transformation et mise en marché : une approche intégrée de la filière piscicole.
> `→ Voir le pôle aquaculture`

**📚 Formation professionnelle**
> Des parcours certifiants, accessibles hors-ligne, conçus pour les réalités du terrain africain.
> `→ Explorer le catalogue`

---

#### `DifferentiationSection` — Fond #1B4332 (vert profond)

**Titre :**
> "Conçu pour les réalités du terrain africain"

**Sous-titre :**
> Nous ne transposons pas des modèles étrangers. Nous développons des solutions nées des contraintes et des opportunités du continent.

**4 points forts (icône check doré) :**
- **Connectivité faible prise en compte** — Nos outils fonctionnent avec un signal limité ou en mode hors-ligne
- **Mobile-first par principe** — 80% de nos utilisateurs accèdent à la plateforme depuis un smartphone
- **Paiements locaux intégrés** — Mobile Money, Orange Money, Wave et virements locaux acceptés
- **Langues et contextes locaux** — Supports traduits, formateurs issus des régions d'intervention

---

#### `TestimonialsSection` — Fond blanc (à activer quand les témoignages sont disponibles)

**Titre :**
> "Ce que disent nos partenaires"

3 témoignages en carousel (avatar initiale + nom + rôle + pays + citation)

---

#### `CtaLeadSection` — Fond dégradé vert

**Titre :**
> "Parlons de votre projet agricole"

**Sous-titre :**
> Que vous soyez producteur, directeur technique d'une ONG, gestionnaire de projet ou bailleur de fonds — notre équipe analyse votre situation et vous propose une réponse adaptée sous 24h.

**CTA :** `Prendre contact →` (doré) | `Voir les formations` (bordure blanche)

---

### 2. À PROPOS & IMPACT — `/about`

#### `HeroInner`
**Badge :** `Notre histoire & nos engagements`
**H1 :** "Nés du terrain, engagés sur le long terme"
**Sous-titre :** AgriFish a été fondé par des praticiens du développement agricole africain, convaincus qu'un accompagnement de qualité change durablement la trajectoire d'une exploitation, d'une coopérative ou d'un projet de développement.

---

#### `StorySection` — Fond blanc, 2 colonnes

**Colonne gauche — Texte :**

**Titre :** "Transformer l'agriculture africaine par l'expertise et la proximité"

> Fondée en 2022, AgriFish est née d'un constat simple : les acteurs agricoles africains manquent rarement de motivation ou de savoir-faire local. Ce qui leur fait défaut, c'est l'accès à des outils adaptés, à une expertise technique de haut niveau et à des formations accessibles sur le terrain.

> Nous avons construit AgriFish pour combler cet écart — en assemblant une équipe pluridisciplinaire d'agronomes, d'aquaculteurs, de formateurs et de spécialistes du développement de projet, tous ancrés dans les réalités africaines.

**2 cartes intégrées :**

🎯 **Notre mission**
> Rendre l'expertise agricole et aquacole accessible à tous les acteurs du secteur, quelle que soit leur taille ou leur localisation.

🔭 **Notre vision**
> Une agriculture africaine autonome, compétitive et durable, portée par des professionnels formés et outillés pour les défis du XXIe siècle.

**Colonne droite — Timeline :**

| Année | Jalon | Description |
|-------|-------|-------------|
| 2022 | Fondation | Naissance du concept à Dakar. Constitution de l'équipe fondatrice. |
| 2023 | Développement | Construction de la plateforme. Premiers projets pilotes en Guinée et au Sénégal. |
| 2024 | Lancement officiel | Ouverture du catalogue de formations. 40 projets accompagnés la première année. |
| 2025 | Expansion régionale | Présence active dans 12 pays. 2 000 professionnels formés. |

---

#### `ValuesSection` — Fond #F5F0E8

**Titre :** "Les valeurs qui guident nos interventions"

**Sous-titre :** Ces principes ne sont pas des déclarations d'intention. Ils structurent notre façon de travailler avec chaque client.

**4 cartes :**

🤝 **Proximité**
> Nous travaillons aux côtés de nos clients, sur le terrain, pas depuis un bureau éloigné. Chaque mission démarre par une phase d'écoute et de diagnostic partagé.

💡 **Innovation adaptée**
> Nous ne plaquons pas des solutions standardisées. Nous adaptons les meilleures pratiques mondiales aux réalités locales — climatiques, économiques et culturelles.

🏆 **Exigence de résultats**
> Nos engagements sont adossés à des indicateurs mesurables. Chaque mission se conclut par un bilan d'impact transparent.

🌍 **Impact durable**
> Nous formons pour rendre autonome, pas pour créer une dépendance. Notre succès se mesure à la capacité de nos clients à progresser sans nous.

---

#### `TeamSection` — Fond blanc

**Titre :** "Une équipe pluridisciplinaire et engagée"

**Sous-titre :** Nos experts combinent formation académique de haut niveau et expérience directe du terrain africain.

**4 membres (grille 2×2) :**

**Kofi Asante** — Fondateur & Directeur général
*Agronome, 15 ans d'expérience en développement rural*
> Spécialité : Stratégie agricole & structuration de filières

**Aminata Diallo** — Directrice technique
*Ingénieure agroalimentaire, ex-FAO*
> Spécialité : Gestion de projets & monitoring terrain

**Jean-Pierre Bah** — Responsable formation
*Formateur certifié, spécialiste e-learning en contexte africain*
> Spécialité : Ingénierie pédagogique & formation à distance

**Fatou Coulibaly** — Directrice relations clients
*MBA Gestion de projet, ancienne coopérative agricole*
> Spécialité : Accompagnement client & développement partenariats

---

#### `PartnersSection` — Fond #F5F0E8 ← NOUVEAU

**Titre :** "Nos partenaires & références institutionnelles"

**Sous-titre :** AgriFish collabore avec des organisations nationales et internationales pour démultiplier son impact.

Logos partenaires en grille (placeholder)

---

#### `CtaLeadSection` — Fond vert profond

**Titre :** "Vous avez un projet ? Parlons-en."
**CTA :** `Contacter l'équipe →`

---

### 3. NOS EXPERTISES — `/services`

#### `HeroInner`
**Badge :** `Agriculture · Aquaculture · Conseil`
**H1 :** "Des expertises sectorielles au service de votre performance"
**Sous-titre :** AgriFish mobilise des compétences spécialisées sur deux filières stratégiques pour l'Afrique subsaharienne : l'agriculture et l'aquaculture. Notre approche combine diagnostic rigoureux, accompagnement terrain et transfert de compétences.

---

#### `AgricultureSection` — Fond blanc

**Badge :** `Pôle 01`
**Titre :** "🌱 Agriculture — Conseil & Accompagnement"
**Intro :**
> De l'exploitation familiale au projet agro-industriel, nous intervenons à chaque niveau de complexité avec la même rigueur méthodologique.

**6 cartes services (grille 3×2) :**

**Conseil & diagnostic agricole**
> Évaluation complète de votre système de production, identification des leviers de performance, recommandations priorisées.
- ✓ Audit technique et économique
- ✓ Cartographie des contraintes
- ✓ Plan d'action opérationnel
- ✓ Suivi post-diagnostic

**Gestion de projets agricoles**
> Planification, coordination et suivi de vos projets agricoles, de la conception à la livraison.
- ✓ Cadre logique & indicateurs
- ✓ Gestion des parties prenantes
- ✓ Reporting bailleur
- ✓ Évaluation d'impact

**Études de faisabilité**
> Analyses technico-économiques rigoureuses pour sécuriser vos décisions d'investissement.
- ✓ Analyse marché locale
- ✓ Business plan détaillé
- ✓ Analyse de risques
- ✓ Recommandation d'investissement

**Suivi & monitoring terrain**
> Dispositifs de collecte de données et de reporting pour piloter vos projets à distance.
- ✓ Indicateurs de performance
- ✓ Collecte mobile de données
- ✓ Tableaux de bord temps réel
- ✓ Rapports périodiques

**Gestion post-récolte**
> Optimisation de la chaîne post-récolte pour réduire les pertes et valoriser la production.
- ✓ Diagnostic des pertes
- ✓ Protocoles de stockage
- ✓ Mise en marché
- ✓ Transformation primaire

**Développement de filières**
> Structuration et animation de filières agricoles : coopératives, plateformes d'agrégation, contractualisation.
- ✓ Structuration organisationnelle
- ✓ Négociation contractuelle
- ✓ Formation des acteurs
- ✓ Accès au financement

---

#### `AquacultureSection` — Fond #F5F0E8

**Badge :** `Pôle 02`
**Titre :** "🐟 Aquaculture — Expertise intégrée"
**Intro :**
> L'aquaculture représente l'un des secteurs à plus fort potentiel de croissance en Afrique. AgriFish vous accompagne de la conception de votre site à la mise en marché de votre production.

**4 cartes (grille 2×2) :**

**Création & aménagement de sites**
> Conception technique de bassins, étangs et infrastructures aquacoles adaptés à votre contexte géographique.
- ◉ Étude de site et d'eau
- ◉ Plans d'aménagement
- ◉ Supervision de construction
- ◉ Mise en service

**Suivi technique & sanitaire**
> Protocoles de gestion sanitaire, alimentation et suivi de croissance pour optimiser votre rendement.
- ◉ Protocoles sanitaires
- ◉ Gestion de l'alimentation
- ◉ Suivi des paramètres d'eau
- ◉ Prévention des épizooties

**Transformation & commercialisation**
> Développement de votre chaîne de valeur aval : transformation, conditionnement, accès aux marchés.
- ◉ Techniques de transformation
- ◉ Normes sanitaires export
- ◉ Accès aux acheteurs
- ◉ Négociation commerciale

**Formation spécialisée aquaculture**
> Modules de formation dédiés aux techniciens et managers de filière piscicole.
- ◉ Pisciculture en bassin
- ◉ Crevetticulture
- ◉ Gestion de ferme aquacole
- ◉ Certification professionnelle

---

#### `ProcessSection` — Fond blanc

**Titre :** "Notre méthode d'intervention"

**Sous-titre :** Un processus structuré qui garantit des résultats mesurables à chaque étape.

**4 étapes :**

**01 — Prise de contact & cadrage**
> Un premier échange pour comprendre votre contexte, vos objectifs et vos contraintes. Sous 48h, nous vous proposons une approche d'intervention adaptée.

**02 — Diagnostic & analyse**
> Collecte de données terrain, entretiens avec les parties prenantes, analyse technique et économique. Cette phase produit un diagnostic partagé et validé avec vous.

**03 — Accompagnement opérationnel**
> Mise en œuvre de la stratégie définie, avec une présence terrain selon les besoins. Reporting régulier et ajustements en temps réel.

**04 — Mesure des résultats & transfert**
> Évaluation des indicateurs d'impact, documentation des acquis, formation des équipes locales pour garantir la pérennité des changements engagés.

---

#### `CtaLeadSection` — Fond vert profond

**Titre :** "Besoin d'une expertise sur votre secteur ?"
**Sous-titre :** Décrivez votre projet en quelques lignes. Notre équipe vous contacte sous 24h pour une première analyse gratuite.
**CTA :** `Demander une analyse gratuite →`

---

### 4. FORMATIONS PROFESSIONNELLES — `/formations`

#### `HeroFormation` — Dégradé vert profond

**Badge :** `Formations certifiantes | Agriculture & Aquaculture`
**H1 :** "Des formations conçues pour les professionnels du secteur agricole africain"
**Sous-titre :** Modules 100% en ligne, accessibles hors-ligne, validés par des experts terrain. Progressez à votre rythme, obtenez une certification reconnue.

**CTA :** `Explorer le catalogue →` (doré) | `Voir les avantages` (bordure blanche)

**4 compteurs :**
- 🎓 **25+** modules disponibles
- 🏆 **100%** en ligne & certifiant
- 📜 **2 000+** professionnels diplômés
- 🌍 **12** pays d'intervention

---

#### `AdvantagesSection` — Fond blanc, design sobre

**Titre :** "Pourquoi nos formations font la différence"

**4 piliers :**

📱 **Mobile-first & hors-ligne**
> Téléchargez vos modules et suivez votre formation sans connexion internet. Conçu pour les zones à faible connectivité.

🎬 **Contenus de qualité professionnelle**
> Vidéos HD tournées sur le terrain africain, présentées par des formateurs experts, avec sous-titres et transcriptions.

📄 **Ressources téléchargeables**
> Fiches techniques, guides pratiques et supports PDF inclus dans chaque module — à conserver et partager.

📜 **Certification vérifiable**
> Chaque parcours se conclut par une évaluation et délivre un certificat numérique vérifiable, reconnu par nos partenaires institutionnels.

---

#### `CatalogSection` — Fond #F5F0E8

**Titre :** "Catalogue de formations"
**Sous-titre :** Filtrez par domaine et trouvez le parcours adapté à votre profil.

**Barre de filtres :** `Tout` · `Agriculture` · `Aquaculture` · `Gestion de projet` · `Business & finance`

**6 CourseCard :**

**Maraîchage tropical intensif** `Agriculture` `Populaire`
> Techniques de production légumière en zone tropicale : variétés adaptées, irrigation, protection phytosanitaire intégrée.
> 📦 8 modules · ⏱ 12h · 📊 Intermédiaire
> `S'inscrire à cette formation →`

**Pisciculture en bassin** `Aquaculture` `Populaire`
> De l'aménagement du bassin à la récolte : maîtrisez les fondamentaux de la pisciculture continentale.
> 📦 6 modules · ⏱ 9h · 📊 Débutant
> `S'inscrire à cette formation →`

**Systèmes d'irrigation efficaces** `Agriculture`
> Micro-irrigation, goutte-à-goutte et gestion de l'eau agricole : réduisez vos coûts et sécurisez votre production.
> 📦 5 modules · ⏱ 7h · 📊 Intermédiaire
> `S'inscrire à cette formation →`

**Gestion financière de l'exploitation** `Business & finance`
> Tenue de comptes, analyse de rentabilité, accès au crédit agricole : gérez votre exploitation comme une entreprise.
> 📦 7 modules · ⏱ 10h · 📊 Tous niveaux
> `S'inscrire à cette formation →`

**Crevetticulture marine** `Aquaculture`
> Spécificités de l'élevage de crevettes : sélection des sites, gestion de l'eau salée, maladies et commercialisation.
> 📦 8 modules · ⏱ 14h · 📊 Avancé
> `S'inscrire à cette formation →`

**Élaborer son plan d'affaires agricole** `Business & finance`
> Méthode complète pour rédiger un business plan solide, convaincre des bailleurs et accéder aux financements.
> 📦 6 modules · ⏱ 8h · 📊 Intermédiaire
> `S'inscrire à cette formation →`

---

#### `HowItWorksSection` — Fond blanc

**Titre :** "Comment accéder à vos formations"

**4 étapes :**

**01 — Choisissez votre parcours**
> Explorez le catalogue, filtrez par domaine et niveau. Chaque fiche formation détaille le programme, la durée et les prérequis.

**02 — Inscrivez-vous en ligne**
> Créez votre compte en 2 minutes. Paiement sécurisé par mobile money ou carte bancaire. Accès immédiat après validation.

**03 — Apprenez à votre rythme**
> Progressez module par module, depuis n'importe quel appareil. Vos avancées sont sauvegardées automatiquement.

**04 — Obtenez votre certification**
> Complétez l'évaluation finale et recevez votre certificat numérique téléchargeable et vérifiable en ligne.

---

#### `CtaLeadSection` — Fond vert profond

**Titre :** "Prêt à développer vos compétences ?"
**Sous-titre :** Rejoignez plus de 2 000 professionnels qui ont déjà certifié leurs compétences avec AgriFish.
**CTA :** `S'inscrire maintenant →` (doré)

---

### 5. ÉTUDES DE CAS — `/etudes-de-cas` ← NOUVELLE PAGE STRATÉGIQUE

> **Objectif :** Rassurer les clients institutionnels (ONG, bailleurs, investisseurs) avec des preuves concrètes de résultats.

#### `HeroInner`
**Badge :** `Références & Impact terrain`
**H1 :** "Des résultats concrets, documentés sur le terrain"
**Sous-titre :** Chaque mission AgriFish fait l'objet d'un suivi rigoureux et d'une mesure d'impact. Découvrez comment nous avons accompagné nos partenaires vers des résultats durables.

---

#### `CasesSection` — Fond #F5F0E8, grille 2 colonnes

**3 études de cas (cards détaillées) :**

**🌱 Structuration d'une filière maraîchère — Guinée Conakry**
*Durée : 18 mois | Bailleur : ONG partenaire*
> Accompagnement de 3 coopératives de productrices de légumes dans la région de Kindia : diagnostic, formation, mise en marché.
> **Résultats :** +45% de revenus moyens · 320 productrices formées · 2 nouveaux débouchés commerciaux ouverts

**🐟 Création d'un site piscicole — Côte d'Ivoire**
*Durée : 12 mois | Client : Entrepreneur privé*
> Étude de faisabilité, conception technique, supervision de construction et lancement de production d'un site piscicole de 8 bassins.
> **Résultats :** Site opérationnel en 10 mois · 4 tonnes/an de production · Rentabilité atteinte au 14e mois

**📚 Déploiement formation — Sénégal & Burkina Faso**
*Durée : 6 mois | Client : Ministère de l'Agriculture*
> Déploiement d'un programme de formation digitale pour 800 agents de vulgarisation agricole dans 2 pays.
> **Résultats :** 800 agents certifiés · 94% de taux de satisfaction · Intégration dans le dispositif national de formation

---

#### `CtaLeadSection`
**Titre :** "Votre projet pourrait être notre prochaine référence."
**CTA :** `Nous présenter votre projet →`

---

### 6. CONTACT — `/contact`

#### `HeroInner`
**Badge :** `Parlons de votre projet`
**H1 :** "Contactez notre équipe d'experts"
**Sous-titre :** Que vous ayez un projet précis ou une simple question, notre équipe s'engage à vous répondre dans les 24 heures ouvrées.

---

#### `ContactBodySection` — Fond #F5F0E8, layout 1/3 + 2/3

**Colonne gauche :**

📧 **Email**
> contact@agrifish.com

📞 **Téléphone**
> +221 XX XXX XX XX

📍 **Localisation**
> Dakar, Sénégal — interventions dans 12 pays

🕐 **Horaires**
> Lun – Ven : 8h00 – 18h00
> Samedi : 9h00 – 14h00
> Dimanche : Fermé

🟢 **Réponse garantie sous 24h** — Pour toute demande de projet, devis ou information.

---

**Colonne droite — Formulaire :**

**Titre :** "Décrivez-nous votre besoin"
**Sous-titre :** *Tous les champs marqués d'un * sont obligatoires.*

- Prénom * | Nom *
- Email professionnel * | Téléphone
- Organisation / Entreprise ← NOUVEAU champ (qualifie le lead)
- Sujet * → options : Conseil agricole / Projet aquacole / Formation / Étude de faisabilité / Devis / Partenariat institutionnel / Autre
- Message * (6 lignes)
- Pays d'intervention ← NOUVEAU champ (segmentation géographique)
- ☐ J'accepte que mes données soient traitées conformément à la politique de confidentialité d'AgriFish *
- Bouton : `Envoyer ma demande →` (vert pleine largeur)

---

#### `FaqSection` — Fond blanc

**Titre :** "Questions fréquentes"

**4 accordéons :**

**Quel est le délai de réponse après une demande ?**
> Notre équipe s'engage à vous répondre dans les 24 heures ouvrées. Pour les demandes complexes nécessitant une proposition commerciale, comptez 3 à 5 jours ouvrés.

**Travaillez-vous avec des petits producteurs individuels ?**
> Oui. Nous adaptons nos formats d'intervention à toutes les tailles de structure — du petit producteur individuel à la coopérative multi-villages ou au projet institutionnel d'envergure nationale.

**Comment suivre une formation sans connexion internet stable ?**
> Nos formations sont conçues pour être téléchargées et suivies hors ligne. L'application mobile AgriFish permet de télécharger les modules et de synchroniser votre progression dès que vous retrouvez une connexion.

**Comment se déroule concrètement un accompagnement terrain ?**
> Après la phase de diagnostic initial (à distance ou sur site), nous définissons ensemble un plan d'intervention avec des étapes, des livrables et des indicateurs de performance clairs. Un expert référent reste votre interlocuteur tout au long de la mission.

---

## 🧩 Composants partagés — Priorités

| Composant | Pages | Priorité |
|-----------|-------|----------|
| `Navbar` | Toutes | 🔴 |
| `Footer` | Toutes | 🔴 |
| `HeroInner` | À propos, Services, Formations, Cas, Contact | 🔴 |
| `CtaLeadSection` | Toutes sauf Contact | 🔴 |
| `ImpactStat` | Accueil, Formations | 🟡 |
| `ServiceCard` | Accueil, Services | 🟡 |
| `CourseCard` | Formations | 🟡 |
| `ProcessStep` | Services, Formations | 🟡 |
| `CaseStudyCard` | Études de cas | 🟡 |
| `FaqAccordion` | Contact | 🟡 |
| `TeamMemberCard` | À propos | 🟢 |
| `TimelineJalon` | À propos | 🟢 |
| `TrustBar` | Accueil | 🟢 |

---

## ⚠️ Points d'attention & recommandations lead gen

**Nouveaux champs Contact :** "Organisation" et "Pays d'intervention" permettent de qualifier et segmenter automatiquement les leads entrants dans votre CRM.

**Page Études de cas :** Prioriser la mise en ligne dès 3 références documentées — c'est la page la plus convaincante pour les clients institutionnels et les bailleurs.

**Suggestion — Widget WhatsApp flottant :** Bouton fixe bas-droite pointant vers un numéro WhatsApp Business. Très efficace pour les prospects mobiles en Afrique.

**Suggestion — Page /contact/merci :** Page de confirmation post-formulaire pour tracker les conversions Google Analytics / Meta Pixel.

**Suggestion — Chatbot qualificateur :** Un bot simple (Crisp ou Tawk.to, gratuits) peut qualifier les leads hors horaires d'ouverture.
