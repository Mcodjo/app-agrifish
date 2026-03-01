@extends('layouts.app')

@section('title', 'Mentions légales')
@section('meta_description', 'Mentions légales du site AgriFish — informations sur l\'éditeur, l\'hébergement, les droits et les cookies.')

@section('content')

<x-hero-inner
    badge="Informations légales"
    title="Mentions <span class='text-gold-light'>légales</span>"
    subtitle="Informations légales relatives au site agrifish.africa"
/>

<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Contenu légal structuré --}}
        <div class="prose prose-lg max-w-none space-y-12">

            {{-- 1. Éditeur --}}
            <div class="bg-cream rounded-2xl p-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-dark mb-5 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-primary text-white text-sm font-bold flex items-center justify-center">1</span>
                    Éditeur du site
                </h2>
                <div class="space-y-2 text-gray-600">
                    <p><span class="font-semibold text-dark">Raison sociale :</span> AgriFish</p>
                    <p><span class="font-semibold text-dark">Forme juridique :</span> [Forme juridique à compléter]</p>
                    <p><span class="font-semibold text-dark">Capital social :</span> [Capital à compléter]</p>
                    <p><span class="font-semibold text-dark">Siège social :</span> Afrique de l'Ouest</p>
                    <p><span class="font-semibold text-dark">Numéro d'immatriculation :</span> [RCCM ou équivalent à compléter]</p>
                    <p><span class="font-semibold text-dark">Téléphone :</span> +225 XX XX XX XX</p>
                    <p><span class="font-semibold text-dark">Email :</span> contact@agrifish.africa</p>
                    <p><span class="font-semibold text-dark">Directeur de la publication :</span> [Nom du responsable à compléter]</p>
                </div>
            </div>

            {{-- 2. Hébergement --}}
            <div class="bg-cream rounded-2xl p-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-dark mb-5 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-primary text-white text-sm font-bold flex items-center justify-center">2</span>
                    Hébergement
                </h2>
                <div class="space-y-2 text-gray-600">
                    <p><span class="font-semibold text-dark">Hébergeur :</span> [Nom de l'hébergeur à compléter]</p>
                    <p><span class="font-semibold text-dark">Adresse :</span> [Adresse de l'hébergeur]</p>
                    <p><span class="font-semibold text-dark">Site :</span> [URL de l'hébergeur]</p>
                </div>
            </div>

            {{-- 3. Propriété intellectuelle --}}
            <div class="bg-cream rounded-2xl p-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-dark mb-5 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-primary text-white text-sm font-bold flex items-center justify-center">3</span>
                    Propriété intellectuelle
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    L'ensemble du contenu de ce site (textes, images, graphismes, logo, icônes, sons, logiciels, etc.) est la propriété exclusive d'AgriFish ou de ses partenaires. Toute reproduction, représentation, modification, publication, adaptation de tout ou partie des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite, sauf autorisation écrite préalable d'AgriFish.
                </p>
                <p class="text-gray-600 leading-relaxed mt-4">
                    La marque <strong>AgriFish</strong> et son logo sont des marques déposées. Toute utilisation non autorisée de ces marques est strictement prohibée.
                </p>
            </div>

            {{-- 4. Limitation de responsabilité --}}
            <div class="bg-cream rounded-2xl p-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-dark mb-5 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-primary text-white text-sm font-bold flex items-center justify-center">4</span>
                    Limitation de responsabilité
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    AgriFish s'efforce de fournir sur ce site des informations aussi précises que possible. Cependant, AgriFish ne pourra être tenu responsable des omissions, inexactitudes et carences dans la mise à jour, qu'elles soient de son fait ou du fait des tiers partenaires qui lui fournissent ces informations.
                </p>
                <p class="text-gray-600 leading-relaxed mt-4">
                    Tous les informations présentées sur le site agrifish.africa sont données à titre indicatif et sont susceptibles d'évoluer. Par ailleurs, les renseignements figurant sur le site agrifish.africa ne sont pas exhaustifs. Ils sont donnés sous réserve de modifications ayant été apportées depuis leur mise en ligne.
                </p>
            </div>

            {{-- 5. Cookies --}}
            <div class="bg-cream rounded-2xl p-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-dark mb-5 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-primary text-white text-sm font-bold flex items-center justify-center">5</span>
                    Cookies
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    La navigation sur le site agrifish.africa est susceptible de provoquer l'installation de cookie(s) sur l'ordinateur de l'utilisateur. Un cookie est un fichier de petite taille, qui ne permet pas l'identification de l'utilisateur, mais qui enregistre des informations relatives à la navigation d'un ordinateur sur un site.
                </p>
                <p class="text-gray-600 leading-relaxed mt-4">
                    Les données obtenues ont pour seul objet de faciliter la navigation sur le site et de permettre des statistiques d'audience. L'utilisateur peut refuser l'utilisation de cookies en paramétrant son navigateur.
                </p>
                <p class="text-gray-600 leading-relaxed mt-4">
                    Pour en savoir plus sur notre utilisation des données personnelles, consultez notre <a href="{{ route('politique-confidentialite') }}" class="text-primary font-medium hover:underline">Politique de confidentialité</a>.
                </p>
            </div>

            {{-- 6. Droit applicable --}}
            <div class="bg-cream rounded-2xl p-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-dark mb-5 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-primary text-white text-sm font-bold flex items-center justify-center">6</span>
                    Droit applicable et attribution de juridiction
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    Tout litige en relation avec l'utilisation du site agrifish.africa est soumis au droit applicable. Il est fait attribution exclusive de juridiction aux tribunaux compétents.
                </p>
                <p class="text-sm text-gray-400 mt-4">Dernière mise à jour : {{ date('d/m/Y') }}</p>
            </div>

        </div>
    </div>
</section>

@endsection
