@extends('layouts.app')

@section('title', 'Contact')
@section('meta_description', 'Contactez l\'équipe AgriFish pour un conseil agricole, un devis ou une inscription en formation. Réponse sous 24h.')

@section('content')

{{-- Hero --}}
<x-hero-inner
    badge="Parlons de votre projet"
    title="Contactez-<span class='text-gold-light'>nous</span>"
    subtitle="Notre équipe vous répond sous 24 heures ouvrables. Posez vos questions ou décrivez votre projet agricole."
/>

{{-- Contact body --}}
<section class="py-24 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-12">

            {{-- Infos --}}
            <div class="lg:col-span-1 space-y-8">
                <div>
                    <h2 class="text-2xl font-bold text-dark mb-6">Restons en contact</h2>
                    <div class="space-y-5">
                        @foreach([
                            [
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                                'label' => 'Email',
                                'value' => 'contact@agrifish.africa',
                            ],
                            [
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>',
                                'label' => 'Téléphone',
                                'value' => '+225 XX XX XX XX',
                            ],
                            [
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
                                'label' => 'Localisation',
                                'value' => 'Afrique de l\'Ouest',
                            ],
                        ] as $info)
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-xl bg-primary-50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $info['icon'] !!}</svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">{{ $info['label'] }}</p>
                                    <p class="text-dark font-semibold mt-0.5">{{ $info['value'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100">
                    <h3 class="font-bold text-dark mb-4">Heures d'ouverture</h3>
                    <div class="space-y-2 text-sm">
                        @foreach([
                            ['Lundi — Vendredi', '8h00 — 18h00'],
                            ['Samedi',           '9h00 — 14h00'],
                            ['Dimanche',         'Fermé'],
                        ] as $h)
                            <div class="flex justify-between">
                                <span class="text-gray-500">{{ $h[0] }}</span>
                                <span class="font-medium text-dark">{{ $h[1] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-primary-dark rounded-2xl p-6 text-white">
                    <p class="text-xl">⚡</p>
                    <h3 class="font-bold mt-2 mb-1">Réponse rapide</h3>
                    <p class="text-white/70 text-sm leading-relaxed">
                        Nous répondons à toutes les demandes sous <strong class="text-gold-light">24 heures ouvrables</strong>.
                    </p>
                </div>
            </div>

            {{-- Formulaire --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl p-8 sm:p-10 border border-gray-100 shadow-sm">
                    <h2 class="text-2xl font-bold text-dark mb-2">Envoyez-nous un message</h2>
                    <p class="text-gray-400 text-sm mb-8">Tous les champs marqués d'un * sont obligatoires.</p>

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                            <p class="text-red-700 font-medium mb-2">Veuillez corriger les erreurs suivantes :</p>
                            <ul class="text-red-600 text-sm space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Prénom *</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}"
                                       placeholder="Jean"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary text-sm transition @error('first_name') border-red-400 @enderror">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom *</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}"
                                       placeholder="Koné"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary text-sm transition @error('last_name') border-red-400 @enderror">
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       placeholder="jean@exemple.com"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary text-sm transition @error('email') border-red-400 @enderror">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}"
                                       placeholder="+225 XX XX XX XX"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary text-sm transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Sujet *</label>
                            <select name="subject"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary text-sm bg-white transition @error('subject') border-red-400 @enderror">
                                <option value="">-- Choisissez un sujet --</option>
                                @foreach([
                                    'Demande de conseil agricole',
                                    'Suivi de projet',
                                    'Aquaculture / Pisciculture',
                                    'Inscription à une formation',
                                    'Demande de devis',
                                    'Partenariat / Investissement',
                                    'Autre',
                                ] as $subj)
                                    <option value="{{ $subj }}" {{ old('subject') === $subj ? 'selected' : '' }}>{{ $subj }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Message *</label>
                            <textarea name="message" rows="6"
                                      placeholder="Décrivez votre projet, vos besoins ou votre question en détail..."
                                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary text-sm transition resize-none @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
                        </div>

                        <div class="flex items-start gap-3">
                            <input type="checkbox" name="consent" id="consent" class="mt-1 w-4 h-4 accent-primary" {{ old('consent') ? 'checked' : '' }}>
                            <label for="consent" class="text-sm text-gray-500">
                                J'accepte que mes données soient traitées par AgriFish pour répondre à ma demande.
                                <a href="{{ route('politique-confidentialite') }}" class="text-primary hover:underline font-medium" target="_blank">Politique de confidentialité</a> *
                            </label>
                        </div>

                        <button type="submit"
                                class="w-full py-4 bg-primary text-white font-bold rounded-xl text-base hover:bg-primary-dark transition flex items-center justify-center gap-2">
                            Envoyer le message
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FAQ rapide --}}
<section class="py-24 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-dark">Questions fréquentes</h2>
        </div>
        <div class="space-y-4">
            @foreach([
                ['Quel est le délai de réponse ?', 'Nous répondons à toutes les demandes sous 24 heures ouvrables, souvent bien avant ce délai.'],
                ['Proposez-vous des services pour les petits producteurs ?', 'Oui, nous avons des offres adaptées à toutes les tailles d\'exploitation, des petits producteurs aux grandes coopératives.'],
                ['Puis-je suivre une formation sans accès internet stable ?', 'Nos formations sont optimisées pour les connexions lentes. Certains contenus peuvent être téléchargés pour un accès hors ligne.'],
                ['Comment se déroule un accompagnement de projet ?', 'Après votre demande, un expert vous contacte pour un diagnostic, puis nous établissons un plan d\'accompagnement personnalisé accessible depuis votre espace client.'],
            ] as $i => $faq)
                <details class="group bg-cream rounded-xl border border-gray-100 overflow-hidden">
                    <summary class="flex items-center justify-between px-6 py-5 cursor-pointer font-semibold text-dark">
                        {{ $faq[0] }}
                        <svg class="w-5 h-5 text-primary transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <p class="px-6 pb-5 text-gray-500 text-sm leading-relaxed">{{ $faq[1] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>

@endsection
