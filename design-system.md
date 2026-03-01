# Design System — AgriFish v2
> Stack : Laravel Blade + Tailwind CSS + Alpine.js
> Ton : Professionnel & institutionnel | Secteur : Agriculture africaine

---

## 🎨 Palette de couleurs

| Rôle | Nom | Hex | Classe Tailwind custom | Usage |
|------|-----|-----|------------------------|-------|
| Primaire | Vert profond | `#1B4332` | `agri-primary` | Navbar, hero, sections sombres, boutons principaux |
| Primaire clair | Vert forêt | `#2D6A4F` | `agri-primary-light` | Dégradés, hovers sur fond vert |
| Accent | Or institutionnel | `#C9973A` | `agri-gold` | CTA principal, badges, icônes check |
| Accent clair | Or doux | `#E8B86D` | `agri-gold-light` | Hover sur boutons dorés |
| Fond crème | Ivoire | `#F5F0E8` | `agri-cream` | Sections alternées, cartes |
| Fond blanc | Blanc pur | `#FFFFFF` | — | Sections principales, cartes |
| Texte principal | Charbon | `#1A1A1A` | `agri-text` | Corps de texte, titres sur fond clair |
| Texte secondaire | Gris ardoise | `#6B7280` | `agri-muted` | Descriptions, labels, métadonnées |
| Bordure | Gris perle | `#E5E7EB` | — | Cartes, séparateurs |
| Succès | Vert check | `#16A34A` | — | Messages de confirmation, badges |
| Erreur | Rouge doux | `#DC2626` | — | Erreurs formulaire |

**Dégradé hero principal :**
```css
background: linear-gradient(135deg, #1B4332 0%, #2D6A4F 60%, #40916C 100%);
```

**Dégradé CTA banner :**
```css
background: linear-gradient(135deg, #1B4332 0%, #2D6A4F 100%);
```

---

## 🔤 Typographie

**Police principale :** `Plus Jakarta Sans` — [Google Fonts](https://fonts.google.com/specimen/Plus+Jakarta+Sans)
> Moderne, lisible, institutionnelle sans être froide. Parfaite pour le secteur du développement.

**Police secondaire :** `Inter` (fallback système)

```html
<!-- Dans le <head> du layout Blade -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
```

| Élément | Taille | Poids | Line-height | Classe Tailwind |
|---------|--------|-------|-------------|-----------------|
| H1 hero | 56px / 3.5rem | 800 | 1.1 | `text-5xl lg:text-6xl font-extrabold leading-tight` |
| H1 inner | 40px / 2.5rem | 700 | 1.2 | `text-4xl font-bold leading-tight` |
| H2 section | 32px / 2rem | 700 | 1.25 | `text-3xl font-bold` |
| H3 carte | 20px / 1.25rem | 600 | 1.3 | `text-xl font-semibold` |
| Corps texte | 16px / 1rem | 400 | 1.7 | `text-base leading-relaxed` |
| Petit texte | 14px / 0.875rem | 400 | 1.5 | `text-sm` |
| Badge | 12px / 0.75rem | 600 | — | `text-xs font-semibold uppercase tracking-wider` |
| Statistique | 48px / 3rem | 800 | 1 | `text-5xl font-extrabold` |

---

## 📐 Espacements & Layout

**Unité de base :** 8px (échelle Tailwind 4 = 16px)

**Grille :** 12 colonnes · max-width `1280px` · gutter `24px`

**Padding sections :**
- Mobile : `py-12 px-4` (48px vertical, 16px horizontal)
- Tablet : `md:py-16 md:px-6`
- Desktop : `lg:py-24 lg:px-8`

**Breakpoints Tailwind :**
| Nom | Taille | Usage |
|-----|--------|-------|
| `sm` | 640px | Petits mobiles → tablettes |
| `md` | 768px | Tablettes |
| `lg` | 1024px | Desktop |
| `xl` | 1280px | Large desktop |

---

## 🧩 Composants standards

### Bouton primaire (doré — CTA fort)
```html
<a href="#" class="inline-flex items-center gap-2 bg-[#C9973A] hover:bg-[#E8B86D] text-white font-semibold text-base px-6 py-3 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
  Libellé du bouton
  <svg><!-- arrow icon --></svg>
</a>
```
- Background : `#C9973A` → hover `#E8B86D`
- Texte : blanc, 600
- Border-radius : `rounded-lg` (8px)
- Padding : `px-6 py-3`
- Ombre : `shadow-md` → `hover:shadow-lg`

### Bouton secondaire (contour blanc — sur fond sombre)
```html
<a href="#" class="inline-flex items-center gap-2 border-2 border-white text-white font-semibold text-base px-6 py-3 rounded-lg hover:bg-white hover:text-[#1B4332] transition-all duration-200">
  Libellé
</a>
```

### Bouton secondaire (contour vert — sur fond clair)
```html
<a href="#" class="inline-flex items-center gap-2 border-2 border-[#1B4332] text-[#1B4332] font-semibold text-base px-6 py-3 rounded-lg hover:bg-[#1B4332] hover:text-white transition-all duration-200">
  Libellé
</a>
```

### Badge de section
```html
<span class="inline-block bg-[#C9973A]/15 text-[#C9973A] text-xs font-semibold uppercase tracking-wider px-3 py-1 rounded-full mb-4">
  Libellé badge
</span>
```

### Carte service (fond blanc)
```html
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
  <div class="w-12 h-12 bg-[#1B4332]/10 rounded-lg flex items-center justify-center mb-4 group-hover:bg-[#1B4332] transition-colors">
    <!-- icône -->
  </div>
  <h3 class="text-xl font-semibold text-[#1A1A1A] mb-2">Titre</h3>
  <p class="text-[#6B7280] text-base leading-relaxed mb-4">Description</p>
  <ul class="space-y-1">
    <li class="flex items-center gap-2 text-sm text-[#6B7280]">
      <span class="w-4 h-4 text-[#C9973A]">✓</span> Sous-item
    </li>
  </ul>
</div>
```

### Carte de formation (CourseCard)
```html
<div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300">
  <div class="h-36 bg-gradient-to-br from-[#1B4332] to-[#40916C] relative">
    <span class="absolute top-3 left-3 bg-white/20 text-white text-xs font-semibold px-2 py-1 rounded-full">Agriculture</span>
    <span class="absolute top-3 right-3 bg-[#C9973A] text-white text-xs font-semibold px-2 py-1 rounded-full">Populaire</span>
  </div>
  <div class="p-5">
    <h3 class="text-lg font-semibold text-[#1A1A1A] mb-1">Titre formation</h3>
    <p class="text-sm text-[#6B7280] leading-relaxed mb-3">Description courte.</p>
    <div class="flex items-center gap-4 text-xs text-[#6B7280] mb-4">
      <span>📦 8 modules</span>
      <span>⏱ 12h</span>
      <span>📊 Intermédiaire</span>
    </div>
    <a href="#" class="block text-center bg-[#1B4332] hover:bg-[#2D6A4F] text-white font-semibold text-sm py-2.5 rounded-lg transition-colors">
      S'inscrire à cette formation →
    </a>
  </div>
</div>
```

### Métrique / Statistique
```html
<div class="text-center">
  <div class="text-5xl font-extrabold text-[#C9973A] mb-1">150+</div>
  <div class="text-base font-semibold text-white">Projets accompagnés</div>
  <div class="text-sm text-white/60">Agriculture & aquaculture</div>
</div>
```

### Étape de processus numérotée
```html
<div class="flex gap-4">
  <div class="flex-shrink-0 w-12 h-12 bg-[#C9973A] text-white font-extrabold text-lg rounded-full flex items-center justify-center">01</div>
  <div>
    <h3 class="text-lg font-semibold text-[#1A1A1A] mb-1">Titre étape</h3>
    <p class="text-[#6B7280] text-base leading-relaxed">Description de l'étape.</p>
  </div>
</div>
```

### Input formulaire
```html
<input type="text"
  class="w-full px-4 py-3 border border-gray-200 rounded-lg text-base text-[#1A1A1A] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#1B4332] focus:border-transparent transition-all"
  placeholder="Votre prénom *">

<!-- État erreur -->
<input type="text" class="... border-red-400 focus:ring-red-400 bg-red-50">
<p class="text-red-500 text-sm mt-1">Ce champ est requis.</p>
```

### Accordéon FAQ (Alpine.js)
```html
<div x-data="{ open: false }" class="border-b border-gray-200">
  <button @click="open = !open" class="w-full flex items-center justify-between py-5 text-left font-semibold text-[#1A1A1A] hover:text-[#1B4332] transition-colors">
    <span>Question ?</span>
    <svg :class="{'rotate-180': open}" class="w-5 h-5 text-[#C9973A] transition-transform duration-200"><!-- chevron --></svg>
  </button>
  <div x-show="open" x-collapse class="pb-5 text-[#6B7280] leading-relaxed">
    Réponse développée.
  </div>
</div>
```

---

## 🧭 Navbar

**Comportement :**
- Sur hero : `bg-transparent` + texte blanc
- Au scroll (>80px) : `bg-white shadow-md` + texte sombre
- Transition : `transition-all duration-300`

**Structure :**
- Logo : icon SVG feuille #1B4332 + texte `Plus Jakarta Sans 700`
- Liens nav : `text-sm font-medium` · hover : `text-[#C9973A]` + `border-b-2 border-[#C9973A]`
- CTA : bouton doré compact `px-4 py-2`
- Mobile : burger Alpine.js · overlay menu slide-down

---

## 🦶 Footer

**Fond :** `#1B4332` (vert profond)
**Texte :** `white/80`
**Liens :** `white/60` → hover `white`
**Séparateur :** `white/10`

**Layout :** 4 colonnes desktop, 2 colonnes tablet, 1 colonne mobile

---

## 🖼️ Style général

| Propriété | Valeur |
|-----------|--------|
| Ton visuel | Institutionnel sobre, touches de chaleur via l'or |
| Ombres | `shadow-sm` par défaut, `shadow-lg` au hover |
| Border-radius | `rounded-lg` (8px) pour les cartes, `rounded-full` pour les badges/avatars |
| Bordures | Fines `border border-gray-100`, pas de bordures décoratives épaisses |
| Icônes | **Heroicons** (v2, outline) — cohérence institutionnelle |
| Images | Photos réalistes de terrain africain (pas d'illustrations vectorielles) |
| Animations | `transition-all duration-200/300` uniquement, pas d'animations extravagantes |
| Vague SVG | Entre hero et première section, couleur `#F5F0E8` ou blanc |

---

## ⚙️ Configuration Tailwind (tailwind.config.js)

```js
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        'agri': {
          'primary':       '#1B4332',
          'primary-light': '#2D6A4F',
          'primary-pale':  '#40916C',
          'gold':          '#C9973A',
          'gold-light':    '#E8B86D',
          'cream':         '#F5F0E8',
          'text':          '#1A1A1A',
          'muted':         '#6B7280',
        }
      },
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
      },
      maxWidth: {
        'content': '1280px',
      }
    },
  },
  plugins: [],
}
```

---

## 🔗 Références design

- **Inspiration institutionnelle :** CGIAR.org, FAO.org (autorité, rigueur)
- **Inspiration modernité :** Stripe.com (clarté, typographie forte)
- **Référence sectorielle africaine :** Agence française de développement (afd.fr)
- **Palette de référence :** Verts forestiers profonds + or chaud (évoque la nature, la valeur, le sérieux)
