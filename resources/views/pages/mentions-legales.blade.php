@extends('layouts.app')

@section('title', 'Mentions légales')
@section('meta_description', 'Mentions légales du site AgriFish : éditeur, hébergement, propriété intellectuelle, crédits photographiques et cookies.')

@php
    $contact = config('site.contact');
    $legal = config('site.legal');
    $host = $legal['hebergeur'];
@endphp

@section('content')
<x-legal-page title="Mentions légales" :updated="$legal['mise_a_jour']" :sections="[
    'editeur' => 'Éditeur du site',
    'hebergement' => 'Hébergement',
    'propriete' => 'Propriété intellectuelle',
    'credits' => 'Crédits photographiques',
    'responsabilite' => 'Responsabilité',
    'donnees' => 'Données personnelles',
    'cookies' => 'Cookies',
    'droit' => 'Droit applicable',
]">
    <h2 id="editeur">Éditeur du site</h2>
    <p>Le site {{ parse_url(config('app.url'), PHP_URL_HOST) ?: 'AgriFish' }} est édité par :</p>
    <ul>
        <li><strong>Raison sociale :</strong> {{ $legal['raison_sociale'] }}</li>
        @foreach([
            'forme' => 'Forme juridique',
            'capital' => 'Capital social',
            'siege' => 'Siège social',
            'immatriculation' => 'Immatriculation',
            'identifiant_fiscal' => 'Identifiant fiscal',
        ] as $key => $label)
            @if($legal[$key])
                <li><strong>{{ $label }} :</strong> {{ $legal[$key] }}</li>
            @endif
        @endforeach
        @if($contact['telephone'])
            <li><strong>Téléphone :</strong> {{ $contact['telephone'] }}</li>
        @endif
        <li><strong>E-mail :</strong> <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></li>
        @if($legal['directeur_publication'])
            <li><strong>Directeur de la publication :</strong> {{ $legal['directeur_publication'] }}</li>
        @endif
    </ul>

    <h2 id="hebergement">Hébergement</h2>
    @if($host['nom'])
        <ul>
            <li><strong>Hébergeur :</strong> {{ $host['nom'] }}</li>
            @if($host['adresse'])<li><strong>Adresse :</strong> {{ $host['adresse'] }}</li>@endif
            @if($host['telephone'])<li><strong>Téléphone :</strong> {{ $host['telephone'] }}</li>@endif
            @if($host['site'])<li><strong>Site :</strong> <a href="{{ $host['site'] }}" rel="noopener" target="_blank">{{ preg_replace('#^https?://#', '', $host['site']) }}</a></li>@endif
        </ul>
    @else
        <p>Les coordonnées de l’hébergeur du site sont communiquées sur simple demande à <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a>.</p>
    @endif

    <h2 id="propriete">Propriété intellectuelle</h2>
    <p>Les textes, graphismes, logos et éléments du site sont la propriété d’AgriFish ou de ses partenaires, sauf mention contraire. Toute reproduction, représentation ou adaptation, totale ou partielle, sans autorisation écrite préalable d’AgriFish est interdite.</p>
    <p>Le nom AgriFish et son logo ne peuvent pas être utilisés sans l’accord d’AgriFish.</p>

    <h2 id="credits">Crédits photographiques</h2>
    <p>Les photographies d’illustration proviennent de banques d’images sous licence libre. Elles ne représentent pas des missions d’AgriFish. Les images sous licence CC BY-SA restent diffusées sous cette même licence après adaptation.</p>
    <ul>
        @foreach(config('site.photos') as $photo)
            <li><a href="{{ $photo['source'] }}" rel="noopener" target="_blank">{{ $photo['titre'] }}</a>, {{ $photo['auteur'] }}, <a href="{{ $photo['licence_url'] }}" rel="noopener" target="_blank">{{ $photo['licence'] }}</a>. Image recadrée et compressée.</li>
        @endforeach
    </ul>

    <h2 id="responsabilite">Responsabilité</h2>
    <p>AgriFish veille à l’exactitude des informations publiées sur ce site, mais ne peut garantir qu’elles sont complètes et à jour. Elles sont données à titre indicatif et peuvent évoluer. AgriFish ne saurait être tenu responsable d’une omission, d’une inexactitude ou d’un défaut de mise à jour.</p>

    <h2 id="donnees">Données personnelles</h2>
    <p>Les données collectées par le formulaire de contact et l’espace client sont traitées par {{ $legal['raison_sociale'] }} pour répondre à vos demandes et suivre vos projets. Leur utilisation, leur durée de conservation et vos droits sont décrits dans la <a href="{{ route('politique-confidentialite') }}">politique de confidentialité</a>.</p>

    <h2 id="cookies">Cookies</h2>
    <p>Le site utilise uniquement les cookies nécessaires à son fonctionnement : maintien de la session, sécurité des formulaires et connexion à l’espace client. Aucun cookie publicitaire n’est déposé.</p>

    <h2 id="droit">Droit applicable</h2>
    <p>Les présentes mentions sont régies par le droit applicable au siège de l’éditeur. En cas de litige relatif à l’utilisation du site, une solution amiable sera recherchée en priorité ; à défaut, les juridictions compétentes du ressort du siège de l’éditeur seront saisies.</p>
</x-legal-page>
@endsection
