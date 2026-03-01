<x-app-layout title="Études de cas & Impact">
    <x-hero-inner 
        badge="Références & Impact terrain" 
        title="Des résultats concrets, documentés sur le terrain"
        subtitle="Chaque mission AgriFish fait l'objet d'un suivi rigoureux et d'une mesure d'impact. Découvrez comment nous avons accompagné nos partenaires vers des résultats durables."
    />

    {{-- CasesSection --}}
    <section class="py-12 md:py-16 lg:py-24 bg-cream">
        <div class="max-w-content mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Case 1 --}}
                <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group flex flex-col">
                    <div class="h-48 bg-primary relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <img src="https://images.unsplash.com/photo-1595841696660-aa986ad443ea?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Guinée Conakry" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute bottom-4 left-4 right-4">
                            <span class="bg-gold text-white text-xs font-bold px-2 py-1 rounded">Agriculture</span>
                            <span class="text-white/80 text-xs ml-2">18 mois</span>
                        </div>
                    </div>
                    <div class="p-6 flex-grow">
                        <h3 class="text-xl font-bold text-dark mb-3">Structuration d'une filière maraîchère — Guinée Conakry</h3>
                        <p class="text-gray-500 text-sm mb-4">Accompagnement de 3 coopératives de productrices de légumes dans la région de Kindia : diagnostic, formation, mise en marché.</p>
                        
                        <div class="bg-cream/50 rounded-lg p-4 mb-4">
                            <h4 class="text-primary font-bold text-xs uppercase tracking-wider mb-2">Résultats clés :</h4>
                            <ul class="space-y-2">
                                <li class="flex items-center gap-2 text-sm text-dark font-medium">
                                    <span class="text-gold">★</span> +45% de revenus moyens
                                </li>
                                <li class="flex items-center gap-2 text-sm text-dark font-medium">
                                    <span class="text-gold">★</span> 320 productrices formées
                                </li>
                                <li class="flex items-center gap-2 text-sm text-dark font-medium">
                                    <span class="text-gold">★</span> 2 nouveaux débouchés ouverts
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Case 2 --}}
                <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group flex flex-col">
                    <div class="h-48 bg-primary relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <img src="https://images.unsplash.com/photo-1534351590666-13e3e96b5017?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Côte d'Ivoire" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute bottom-4 left-4 right-4">
                            <span class="bg-gold text-white text-xs font-bold px-2 py-1 rounded">Aquaculture</span>
                            <span class="text-white/80 text-xs ml-2">12 mois</span>
                        </div>
                    </div>
                    <div class="p-6 flex-grow">
                        <h3 class="text-xl font-bold text-dark mb-3">Création d'un site piscicole — Côte d'Ivoire</h3>
                        <p class="text-gray-500 text-sm mb-4">Étude de faisabilité, conception technique, supervision de construction et lancement de production d'un site piscicole de 8 bassins.</p>
                        
                        <div class="bg-cream/50 rounded-lg p-4 mb-4">
                            <h4 class="text-primary font-bold text-xs uppercase tracking-wider mb-2">Résultats clés :</h4>
                            <ul class="space-y-2">
                                <li class="flex items-center gap-2 text-sm text-dark font-medium">
                                    <span class="text-gold">★</span> Site opérationnel en 10 mois
                                </li>
                                <li class="flex items-center gap-2 text-sm text-dark font-medium">
                                    <span class="text-gold">★</span> 4 tonnes/an de production
                                </li>
                                <li class="flex items-center gap-2 text-sm text-dark font-medium">
                                    <span class="text-gold">★</span> Rentabilité au 14e mois
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Case 3 --}}
                <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group flex flex-col">
                    <div class="h-48 bg-primary relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <img src="https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Formation" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute bottom-4 left-4 right-4">
                            <span class="bg-gold text-white text-xs font-bold px-2 py-1 rounded">Formation</span>
                            <span class="text-white/80 text-xs ml-2">6 mois</span>
                        </div>
                    </div>
                    <div class="p-6 flex-grow">
                        <h3 class="text-xl font-bold text-dark mb-3">Déploiement formation — Sénégal & Burkina Faso</h3>
                        <p class="text-gray-500 text-sm mb-4">Déploiement d'un programme de formation digitale pour 800 agents de vulgarisation agricole dans 2 pays.</p>
                        
                        <div class="bg-cream/50 rounded-lg p-4 mb-4">
                            <h4 class="text-primary font-bold text-xs uppercase tracking-wider mb-2">Résultats clés :</h4>
                            <ul class="space-y-2">
                                <li class="flex items-center gap-2 text-sm text-dark font-medium">
                                    <span class="text-gold">★</span> 800 agents certifiés
                                </li>
                                <li class="flex items-center gap-2 text-sm text-dark font-medium">
                                    <span class="text-gold">★</span> 94% satisfaction apprenants
                                </li>
                                <li class="flex items-center gap-2 text-sm text-dark font-medium">
                                    <span class="text-gold">★</span> Reconnaissance étatique
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-cta-banner 
        title="Votre projet pourrait être notre prochaine référence."
        subtitle="Nos experts analysent vos besoins et vous accompagnent vers des résultats mesurables."
        btnLabel="Nous présenter votre projet →"
        btnRoute="/contact"
    />
</x-app-layout>
