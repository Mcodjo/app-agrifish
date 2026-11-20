@extends('layouts.app')

@section('title', 'Mentions légales')
@section('meta_description', 'Mentions légales du site AgriFish : éditeur, hébergement, propriété intellectuelle, crédits photographiques et cookies.')

@php $contact = config('site.contact'); @endphp

@section('content')
<x-legal-page title="Mentions légales" updated="20 novembre 2026" :sections="[
    'editeur' => 'Éditeur du site',
    'hebergement' => 'Hébergement',
    'propriete' => 'Propriété intellectuelle',
    'credits' => 'Crédits photographiques',
    'responsabilite' => 'Responsabilité',
    'cookies' => 'Cookies',
    'droit' => 'Droit applicable',
]">
    <h2 id="editeur">Éditeur du site</h2>
    <ul>
        <li><strong>Raison sociale :</strong> AgriFish</li>
        <li><strong>Forme juridique :</strong> [à compléter]</li>
        <li><strong>Capital social :</strong> [à compléter]</li>
        <li><strong>Siège social :</strong> [adresse à compléter]</li>
        <li><strong>Immatriculation :</strong> [RCCM ou équivalent à compléter]</li>
        @if($contact['telephone'])
            <li><strong>Téléphone :</strong> {{ $contact['telephone'] }}</li>
        @endif
        <li><strong>E-mail :</strong> <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></li>
        <li><strong>Directeur de la publication :</strong> [nom à compléter]</li>
    </ul>

    <h2 id="hebergement">Hébergement</h2>
    <ul>
        <li><strong>Hébergeur :</strong> [nom à compléter]</li>
        <li><strong>Adresse :</strong> [adresse à compléter]</li>
        <li><strong>Site :</strong> [adresse web à compléter]</li>
    </ul>

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

    <h2 id="cookies">Cookies</h2>
    <p>Le site utilise uniquement les cookies nécessaires à son fonctionnement : maintien de la session, sécurité des formulaires et connexion à l’espace client. Aucun cookie publicitaire n’est déposé.</p>
    <p>Pour en savoir plus sur le traitement de vos données, consultez la <a href="{{ route('politique-confidentialite') }}">politique de confidentialité</a>.</p>

    <h2 id="droit">Droit applicable</h2>
    <p>Les présentes mentions sont régies par le droit applicable au siège de l’éditeur. Tout litige relatif à l’utilisation du site relève des juridictions compétentes de ce ressort.</p>
</x-legal-page>
@endsection
