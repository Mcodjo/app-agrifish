# Copilot Instructions — AgriFish v2

## Stack technique
- **Backend :** Laravel 11 + Blade templating
- **Styles :** Tailwind CSS v3 (config étendue — voir design-system.md)
- **Interactivité :** Alpine.js v3 (pas de Vue, pas de React)
- **Icônes :** Heroicons v2 (outline en priorité, solid pour les éléments actifs)
- **Polices :** Plus Jakarta Sans (Google Fonts) + Inter (fallback)

---

## Règles de code frontend Blade + Tailwind

### Structure des fichiers Blade
- Layout principal : `resources/views/layouts/app.blade.php`
- Composants réutilisables : `resources/views/components/`
  - Nommage : `kebab-case.blade.php` (ex: `hero-inner.blade.php`, `cta-banner.blade.php`)
- Pages : `resources/views/pages/`
- Partials : `resources/views/partials/`
- Appel composant : `<x-hero-inner title="..." badge="..." />`

### Tailwind CSS
- Utiliser les classes Tailwind en priorité, jamais de style inline
- **Mobile-first obligatoire** : styles base mobile, overrides `md:` et `lg:`
- Breakpoints du projet :
  - Mobile : base (< 768px)
  - Tablet : `md:` (≥ 768px)
  - Desktop : `lg:` (≥ 1024px)
  - Large : `xl:` (≥ 1280px)
- **Couleurs du design system — utiliser UNIQUEMENT ces valeurs :**
  - Primaire : `bg-agri-primary` / `text-agri-primary` → `#1B4332`
  - Primaire clair : `bg-agri-primary-light` → `#2D6A4F`
  - Or/accent : `bg-agri-gold` / `text-agri-gold` → `#C9973A`
  - Fond crème : `bg-agri-cream` → `#F5F0E8`
  - Texte : `text-agri-text` → `#1A1A1A`
  - Texte secondaire : `text-agri-muted` → `#6B7280`
- Container max-width : `max-w-content mx-auto px-4 md:px-6 lg:px-8`
- Padding sections : `py-12 md:py-16 lg:py-24`

### Alpine.js
- Utiliser `x-data`, `x-show`, `x-bind`, `@click`, `@scroll.window` uniquement
- Pas de logique métier dans Alpine — uniquement UI (toggle, scroll, filtres visuels)
- Burger menu : `x-data="{ open: false }"` avec `x-show="open"` et `x-transition`
- Navbar scroll : `x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 80"`
- Filtres formations : `x-data="{ active: 'all' }"` avec `:class` et `x-show`
- Accordéons FAQ : `x-data="{ open: false }"` avec `x-show` et `x-collapse`

### Composants Blade — Conventions
```blade
{{-- resources/views/components/cta-banner.blade.php --}}
@props([
    'title'    => 'Titre par défaut',
    'subtitle' => '',
    'cta'      => 'Nous contacter',
    'ctaUrl'   => '/contact',
    'ctaAlt'   => '',
    'ctaAltUrl'=> '',
])

<section class="bg-agri-primary py-20">
    <div class="max-w-content mx-auto px-4 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">{{ $title }}</h2>
        @if($subtitle)
            <p class="text-white/70 text-lg max-w-2xl mx-auto mb-8">{{ $subtitle }}</p>
        @endif
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ $ctaUrl }}" class="inline-flex items-center justify-center gap-2 bg-agri-gold hover:bg-agri-gold-light text-white font-semibold px-6 py-3 rounded-lg transition-all duration-200 shadow-md">
                {{ $cta }}
            </a>
            @if($ctaAlt)
                <a href="{{ $ctaAltUrl }}" class="inline-flex items-center justify-center gap-2 border-2 border-white text-white font-semibold px-6 py-3 rounded-lg hover:bg-white hover:text-agri-primary transition-all duration-200">
                    {{ $ctaAlt }}
                </a>
            @endif
        </div>
    </div>
</section>
```

### Formulaires
- Validation : toujours côté serveur avec `$errors` Laravel
- Erreurs inline : `@error('field') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror`
- Input en erreur : ajouter `{{ $errors->has('field') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}`
- Token CSRF : `@csrf` dans tout `<form>`
- Méthode : `method="POST"` + `@method('PUT')` si nécessaire

### Accessibilité
- `aria-label` sur tous les boutons sans texte visible (burger, icônes seules)
- `alt` descriptif sur toutes les images
- Contraste WCAG AA minimum (vérifié par la palette du design system)
- Navigation clavier : tous les éléments interactifs accessibles via Tab
- `role="navigation"`, `role="main"`, `role="contentinfo"` sur les landmarks HTML

### Performance
- Images : utiliser `loading="lazy"` sur toutes les images hors-viewport
- Polices : `display=swap` sur Google Fonts
- Pas de JavaScript inline, pas de CSS inline

---

## Ce que Copilot doit TOUJOURS faire
- Respecter la palette agri-* définie dans tailwind.config.js
- Générer des composants Blade responsifs mobile-first par défaut
- Utiliser Alpine.js pour toute interactivité UI légère
- Ajouter `transition-all duration-200` ou `duration-300` sur les hovers
- Inclure les états hover et focus dans les composants interactifs
- Utiliser `max-w-content mx-auto` pour tous les conteneurs de sections
- Toujours valider les formulaires côté serveur avec les conventions Laravel

## Ce que Copilot ne doit PAS faire
- Utiliser des couleurs hexadécimales hardcodées différentes de la charte
- Créer des composants non responsifs (mobile-first obligatoire)
- Utiliser `px` pour les tailles de police (Tailwind gère les rem)
- Ajouter des dépendances JavaScript tierces (jQuery, Bootstrap, Vue, React)
- Ignorer l'accessibilité (alt, aria-label, focus)
- Écrire du CSS custom dans des fichiers `.css` séparés sauf cas exceptionnel justifié

---

## Design system — Résumé rapide

```
Primaire   : #1B4332 (vert profond)
Accent     : #C9973A (or institutionnel)
Fond crème : #F5F0E8
Texte      : #1A1A1A
Muted      : #6B7280

Police     : Plus Jakarta Sans (400/500/600/700/800)
H1 hero    : text-5xl lg:text-6xl font-extrabold
H2 section : text-3xl font-bold
H3 carte   : text-xl font-semibold
Corps      : text-base leading-relaxed

Sections   : py-12 md:py-16 lg:py-24
Container  : max-w-content mx-auto px-4 lg:px-8
Cards      : rounded-xl shadow-sm border border-gray-100
Hover card : hover:shadow-lg hover:-translate-y-1 transition-all duration-300
```
