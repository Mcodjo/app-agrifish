@extends('layouts.app')

@section('title', 'Politique de confidentialité')
@section('meta_description', 'Politique de confidentialité d\'AgriFish — comment nous collectons, utilisons et protégeons vos données personnelles.')

@section('content')

<x-hero-inner
    badge="Protection des données"
    title="Politique de <span class='text-gold-light'>confidentialité</span>"
    subtitle="Comment nous collectons, utilisons et protégeons vos données personnelles."
/>

<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-10">

            {{-- Intro --}}
            <div class="bg-primary-50 border border-primary-100 rounded-2xl p-6 flex items-start gap-4">
                <svg class="w-6 h-6 text-primary shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <p class="text-primary-dark leading-relaxed">
                    AgriFish s'engage à protéger la vie privée de ses utilisateurs. Cette politique de confidentialité vous explique quelles données nous collectons, pourquoi nous les collectons et comment vous pouvez les contrôler.
                </p>
            </div>

            @foreach([
                [
                    'num'   => '1',
                    'title' => 'Données collectées',
                    'content' => '<p>Nous collectons les données suivantes lorsque vous utilisez notre site et remplissez notre formulaire de contact :</p>
<ul class="mt-4 space-y-2">
    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Nom et prénom</li>
    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Adresse email</li>
    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Numéro de téléphone (optionnel)</li>
    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Contenu du message et sujet</li>
    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Données de navigation (adresse IP, navigateur, pages visitées via les cookies)</li>
</ul>',
                ],
                [
                    'num'   => '2',
                    'title' => 'Finalité du traitement',
                    'content' => '<p>Vos données sont collectées pour les finalités suivantes :</p>
<ul class="mt-4 space-y-2">
    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-gold shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Répondre à vos demandes de contact et de renseignements</li>
    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-gold shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Traiter vos demandes de services et de formations</li>
    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-gold shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Améliorer nos services et l\'expérience utilisateur</li>
    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-gold shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Vous envoyer des communications marketing avec votre consentement préalable</li>
</ul>',
                ],
                [
                    'num'   => '3',
                    'title' => 'Base légale du traitement',
                    'content' => '<p>Le traitement de vos données est fondé sur :</p>
<ul class="mt-4 space-y-2 text-gray-600">
    <li>• <strong>Votre consentement</strong> : accordé via la case à cocher du formulaire de contact</li>
    <li>• <strong>L\'exécution d\'un contrat</strong> : lorsque vous souscrivez à un service ou une formation</li>
    <li>• <strong>Nos intérêts légitimes</strong> : amélioration de nos services et sécurité du site</li>
</ul>',
                ],
                [
                    'num'   => '4',
                    'title' => 'Durée de conservation',
                    'content' => '<p class="text-gray-600">Vos données personnelles collectées via le formulaire de contact sont conservées pendant <strong>3 ans</strong> à compter de votre dernière interaction avec AgriFish. Les données de navigation sont conservées pendant <strong>13 mois</strong> maximum.</p>',
                ],
                [
                    'num'   => '5',
                    'title' => 'Partage des données',
                    'content' => '<p class="text-gray-600">AgriFish ne vend jamais vos données personnelles à des tiers. Vos données peuvent être partagées uniquement avec nos prestataires techniques (hébergement, messagerie) dans le strict cadre de la prestation de services, et dans le respect des obligations légales applicables.</p>',
                ],
                [
                    'num'   => '6',
                    'title' => 'Vos droits',
                    'content' => '<p>Conformément à la réglementation applicable sur la protection des données, vous disposez des droits suivants :</p>
<div class="mt-4 grid sm:grid-cols-2 gap-3">
    <div class="bg-white border border-gray-100 rounded-lg p-3"><p class="font-semibold text-dark text-sm">Droit d\'accès</p><p class="text-gray-500 text-xs mt-1">Obtenir une copie de vos données</p></div>
    <div class="bg-white border border-gray-100 rounded-lg p-3"><p class="font-semibold text-dark text-sm">Droit de rectification</p><p class="text-gray-500 text-xs mt-1">Corriger des données inexactes</p></div>
    <div class="bg-white border border-gray-100 rounded-lg p-3"><p class="font-semibold text-dark text-sm">Droit à l\'effacement</p><p class="text-gray-500 text-xs mt-1">Demander la suppression de vos données</p></div>
    <div class="bg-white border border-gray-100 rounded-lg p-3"><p class="font-semibold text-dark text-sm">Droit d\'opposition</p><p class="text-gray-500 text-xs mt-1">Vous opposer au traitement marketing</p></div>
</div>
<p class="mt-4 text-gray-600">Pour exercer ces droits, contactez notre DPO à : <a href="mailto:privacy@agrifish.africa" class="text-primary font-medium hover:underline">privacy@agrifish.africa</a></p>',
                ],
                [
                    'num'   => '7',
                    'title' => 'Sécurité des données',
                    'content' => '<p class="text-gray-600">AgriFish met en œuvre toutes les mesures techniques et organisationnelles appropriées pour protéger vos données personnelles contre la perte, l\'utilisation abusive, l\'accès non autorisé, la divulgation, l\'altération ou la destruction. Le site est sécurisé par protocole HTTPS.</p>',
                ],
                [
                    'num'   => '8',
                    'title' => 'Contact & DPO',
                    'content' => '<p class="text-gray-600">Pour toute question relative à cette politique de confidentialité ou pour exercer vos droits, vous pouvez nous contacter :</p>
<div class="mt-4 space-y-2 text-gray-600">
    <p>📧 <strong>Email :</strong> privacy@agrifish.africa</p>
    <p>📮 <strong>Courrier :</strong> AgriFish — DPO, [Adresse postale]</p>
</div>
<p class="mt-4 text-sm text-gray-400">Dernière mise à jour : {{ date("d/m/Y") }}</p>',
                ],
            ] as $section)
                <div class="bg-cream rounded-2xl p-8 border border-gray-100">
                    <h2 class="text-2xl font-bold text-dark mb-5 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-primary text-white text-sm font-bold flex items-center justify-center">{{ $section['num'] }}</span>
                        {{ $section['title'] }}
                    </h2>
                    <div class="text-gray-600 leading-relaxed">{!! $section['content'] !!}</div>
                </div>
            @endforeach

        </div>
    </div>
</section>

@endsection
