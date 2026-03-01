<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="@yield('meta_description', 'AgriFish — Plateforme digitale pour l\'agriculture et l\'aquaculture en Afrique')">
    <title>@yield('title', 'AgriFish') — Plateforme Digitale Agricole</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-gray-900">

    <!-- ===== NAVBAR ===== -->
    <header x-data="{ open: false, scrolled: false }"
            x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 30)"
            :class="scrolled ? 'bg-white shadow-lg' : 'bg-transparent'"
            class="fixed top-0 inset-x-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18 py-4">

                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center shadow">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                        </svg>
                    </div>
                    <span :class="scrolled ? 'text-primary-dark' : 'text-white'"
                          class="text-xl font-bold tracking-tight transition-colors">
                        Agri<span class="text-gold">Fish</span>
                    </span>
                </a>

                <!-- Nav desktop -->
                <nav class="hidden md:flex items-center gap-1">
                    @php
                        $navLinks = [
                            ['route' => 'home',         'label' => 'Accueil'],
                            ['route' => 'about',        'label' => 'À propos'],
                            ['route' => 'services',     'label' => 'Nos services'],
                            ['route' => 'formation',    'label' => 'Formation'],
                            ['route' => 'case-studies', 'label' => 'Impact'],
                            ['route' => 'contact',      'label' => 'Contact'],
                        ];
                    @endphp
                    @foreach($navLinks as $link)
                        <a href="{{ route($link['route']) }}"
                           :class="scrolled ? 'text-gray-700 hover:text-primary' : 'text-white/90 hover:text-white'"
                           class="px-4 py-2 text-sm font-medium rounded-lg hover:bg-white/10 transition-all
                                  {{ request()->routeIs($link['route']) ? 'font-semibold' : '' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>

                <!-- CTA desktop -->
                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('contact') }}"
                       class="px-5 py-2.5 bg-gold text-white text-sm font-semibold rounded-lg shadow hover:bg-gold-dark">
                        Demander un service
                    </a>
                </div>

                <!-- CTA mobile (toujours visible) + Burger -->
                <div class="md:hidden flex items-center gap-2">
                    <a href="{{ route('contact') }}"
                       :class="scrolled ? 'bg-gold' : 'bg-white/20 border border-white/30'"
                       class="px-3 py-1.5 text-white text-xs font-semibold rounded-lg transition-colors">
                        Service
                    </a>
                    <button @click="open = !open"
                            :class="scrolled ? 'text-gray-700' : 'text-white'"
                            class="p-2">
                    <svg x-show="!open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menu mobile -->
        <div x-show="open" x-transition class="md:hidden bg-primary-dark/95 backdrop-blur-sm px-4 pb-4">
            @foreach($navLinks as $link)
                <a href="{{ route($link['route']) }}"
                   class="block px-4 py-3 text-white font-medium border-b border-white/10 hover:text-gold">
                    {{ $link['label'] }}
                </a>
            @endforeach
            <a href="{{ route('contact') }}"
               class="mt-4 block text-center px-5 py-3 bg-gold text-white font-semibold rounded-lg">
                Demander un service
            </a>
        </div>
    </header>

    <!-- ===== CONTENT ===== -->
    <main>
        @yield('content')
    </main>

    <!-- ===== EXIT INTENT MODAL ===== -->
    <div x-data="{ 
            showModal: false, 
            hasShown: false,
            init() {
                // Ne montrer que sur Services et Formation
                const paths = ['/services', '/formation'];
                if (!paths.includes(window.location.pathname)) return;
                
                document.body.addEventListener('mouseleave', (e) => {
                    if (e.clientY < 0 && !this.hasShown) {
                        this.showModal = true;
                        this.hasShown = true;
                    }
                });
            }
         }"
         x-show="showModal"
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-primary-dark/80 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden relative" @click.away="showModal = false">
            <button @click="showModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-dark">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-gold/10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-dark mb-4">Un projet en tête ?</h3>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    Nos experts sont à votre disposition pour analyser vos besoins et vous proposer une solution sur mesure. Profitez d'un premier diagnostic gratuit par nos agronomes ou aquaculteurs.
                </p>
                <div class="flex flex-col gap-3">
                    <a href="{{ route('contact') }}" class="block w-full py-4 bg-primary hover:bg-primary-light text-white font-bold rounded-xl shadow-lg transition-all">
                        Demander mon diagnostic gratuit
                    </a>
                    <button @click="showModal = false" class="text-sm text-gray-500 hover:text-primary font-medium">
                        Non merci, je continue ma visite
                    </button>
                </div>
            </div>
            <div class="bg-cream py-3 px-8 text-center text-xs text-primary font-semibold">
                ⚡️ Réponse garantie sous 24h ouvrées
            </div>
        </div>
    </div>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-dark text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

                <!-- Brand -->
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold">Agri<span class="text-gold">Fish</span></span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-5">
                        La plateforme digitale intelligente pour l'agriculture et l'aquaculture en Afrique.
                    </p>
                    <div class="flex gap-3">
                        <a href="#" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Liens rapides -->
                <div>
                    <h4 class="text-white font-semibold mb-5 text-sm uppercase tracking-wider">Navigation</h4>
                    <ul class="space-y-3">
                        @foreach([['route'=>'home','label'=>'Accueil'], ['route'=>'about','label'=>'À propos'], ['route'=>'services','label'=>'Nos services'], ['route'=>'formation','label'=>'Formation'], ['route'=>'case-studies','label'=>'Impact'], ['route'=>'contact','label'=>'Contact']] as $link)
                            <li><a href="{{ route($link['route']) }}" class="text-gray-400 hover:text-gold text-sm">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h4 class="text-white font-semibold mb-5 text-sm uppercase tracking-wider">Services</h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li class="hover:text-gold cursor-pointer">Conseil agricole</li>
                        <li class="hover:text-gold cursor-pointer">Gestion de projets</li>
                        <li class="hover:text-gold cursor-pointer">Aquaculture & pisciculture</li>
                        <li class="hover:text-gold cursor-pointer">Études de faisabilité</li>
                        <li class="hover:text-gold cursor-pointer">Formation digitale</li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="text-white font-semibold mb-5 text-sm uppercase tracking-wider">Contact</h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-gold mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Afrique de l'Ouest</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-gold mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>contact@agrifish.africa</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-gold mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>+225 XX XX XX XX</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-gray-500 text-sm">© {{ date('Y') }} AgriFish. Tous droits réservés.</p>
                <div class="flex items-center gap-5 text-sm">
                    <a href="{{ route('mentions-legales') }}" class="text-gray-500 hover:text-gold">Mentions légales</a>
                    <a href="{{ route('politique-confidentialite') }}" class="text-gray-500 hover:text-gold">Confidentialité</a>
                    <a href="{{ route('contact') }}" class="text-gray-500 hover:text-gold">Contact</a>
                </div>
                <p class="text-gray-600 text-sm">Conçu pour l'agriculture africaine 🌍</p>
            </div>
        </div>
    </footer>

    <!-- ===== WIDGET WHATSAPP FLOTTANT ===== -->
    <a href="https://wa.me/2250000000000?text=Bonjour%20AgriFish%2C%20je%20voudrais%20des%20informations%20sur%20vos%20services."
       target="_blank" rel="noopener noreferrer"
       class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-[#25D366] rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition-transform"
       title="Nous contacter sur WhatsApp">
        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>

    <!-- Alpine.js pour le menu mobile et les interactions -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
