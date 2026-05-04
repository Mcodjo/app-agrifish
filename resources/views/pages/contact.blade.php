@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])
@section('og_title', $seo['title'])
@section('og_description', $seo['description'])
@section('og_url', $seo['url'])

@section('content')
<section class="relative overflow-hidden bg-primary-dark text-white">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 36px 36px;"></div>
    <div class="max-w-content mx-auto px-4 lg:px-8 pt-20 pb-20 lg:pt-28 lg:pb-24 relative z-10">
        <div class="max-w-3xl">
            <p class="text-sm font-bold uppercase tracking-[0.24em] text-gold-light">Contact</p>
            <h1 class="mt-4 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-7xl">Parlez-nous de votre besoin.</h1>
            <p class="mt-6 text-lg leading-8 text-white/78 lg:text-xl">Demande de conseil, étude technique, projet agricole ou aquacole, formation: nous revenons vers vous rapidement.</p>
        </div>
    </div>
</section>

<section class="bg-cream py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr]">
            <div class="space-y-6">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <h2 class="text-2xl font-bold text-dark">Coordonnées</h2>
                    <div class="mt-5 space-y-4 text-sm text-gray-600">
                        <p><strong class="text-dark">Email</strong><br>contact@agrifish.africa</p>
                        <p><strong class="text-dark">Téléphone</strong><br>+225 XX XX XX XX</p>
                        <p><strong class="text-dark">Zone</strong><br>Afrique de l’Ouest</p>
                    </div>
                </div>

                <div class="rounded-3xl bg-primary-dark p-6 text-white shadow-lg">
                    <h3 class="text-xl font-bold">Délai de réponse</h3>
                    <p class="mt-3 text-white/75 leading-7">Nous répondons généralement sous 24 heures ouvrables, avec un premier cadrage si le besoin est clair.</p>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100 sm:p-8">
                <h2 class="text-2xl font-bold text-dark">Formulaire de contact</h2>
                <p class="mt-2 text-sm text-gray-500">Tous les champs marqués d’un * sont obligatoires.</p>

                @if($errors->any())
                    <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
                        <p class="font-semibold">Veuillez corriger les erreurs ci-dessous.</p>
                        <ul class="mt-2 space-y-1 text-sm">
                            @foreach($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('contact.send') }}" method="POST" class="mt-8 space-y-5">
                    @csrf
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="first_name" class="mb-2 block text-sm font-semibold text-gray-700">Prénom *</label>
                            <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Jean">
                        </div>
                        <div>
                            <label for="last_name" class="mb-2 block text-sm font-semibold text-gray-700">Nom *</label>
                            <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Koné">
                        </div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email *</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="jean@exemple.com">
                        </div>
                        <div>
                            <label for="phone" class="mb-2 block text-sm font-semibold text-gray-700">Téléphone</label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="+225 XX XX XX XX">
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="mb-2 block text-sm font-semibold text-gray-700">Sujet *</label>
                        <select id="subject" name="subject" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="">-- Choisissez un sujet --</option>
                            @foreach([
                                'Demande de conseil agricole',
                                'Suivi de projet',
                                'Aquaculture / Pisciculture',
                                'Étude technique',
                                'Demande de devis',
                                'Partenariat / Investissement',
                                'Autre',
                            ] as $option)
                                <option value="{{ $option }}" {{ old('subject') === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="message" class="mb-2 block text-sm font-semibold text-gray-700">Message *</label>
                        <textarea id="message" name="message" rows="7" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Décrivez votre projet, vos besoins ou vos questions...">{{ old('message') }}</textarea>
                    </div>

                    <div class="flex items-start gap-3">
                        <input id="consent" name="consent" type="checkbox" class="mt-1 h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary" {{ old('consent') ? 'checked' : '' }}>
                        <label for="consent" class="text-sm leading-6 text-gray-600">J’accepte que mes données soient traitées pour répondre à ma demande et j’ai lu la <a href="{{ route('politique-confidentialite') }}" class="font-semibold text-primary hover:underline" target="_blank">politique de confidentialité</a>.</label>
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-4 font-bold text-white transition hover:bg-primary-dark">Envoyer le message</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
