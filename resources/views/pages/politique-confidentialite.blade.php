@extends('layouts.app')

@section('title', 'Politique de confidentialité')
@section('meta_description', 'Politique de confidentialité d’AgriFish : données collectées, finalités, durée de conservation et exercice de vos droits.')

@php $legal = config('site.legal'); @endphp

@section('content')
<x-legal-page title="Politique de confidentialité" :updated="$legal['mise_a_jour']"
              intro="Cette politique explique quelles données AgriFish collecte, pourquoi, combien de temps elles sont conservées et comment exercer vos droits."
              :sections="[
                  'responsable' => 'Responsable du traitement',
                  'donnees' => 'Données collectées',
                  'finalites' => 'Finalités',
                  'base-legale' => 'Base légale',
                  'conservation' => 'Durée de conservation',
                  'partage' => 'Partage des données',
                  'droits' => 'Vos droits',
                  'securite' => 'Sécurité',
                  'contact' => 'Contact',
              ]">
    <h2 id="responsable">Responsable du traitement</h2>
    <p>Le responsable du traitement est {{ $legal['raison_sociale'] }}{{ $legal['siege'] ? ', '.$legal['siege'] : '' }}. Pour toute question sur vos données, écrivez à <a href="mailto:{{ $legal['email_donnees'] }}">{{ $legal['email_donnees'] }}</a>.</p>

    <h2 id="donnees">Données collectées</h2>
    <p>Nous collectons les données suivantes :</p>
    <ul>
        <li>Formulaire de contact : nom, prénom, adresse e-mail, téléphone (facultatif), organisation (facultatif), sujet et contenu du message.</li>
        <li>Espace client : informations de compte (nom, e-mail, téléphone, préférences de contact), demandes de service, documents transmis, messages échangés avec nos experts et historique des actions sur chaque demande (dépôt, changements de statut, assignation, annulation).</li>
        <li>Données techniques : adresse IP et informations de connexion nécessaires à la sécurité du site.</li>
    </ul>

    <h2 id="finalites">Finalités</h2>
    <ul>
        <li>Répondre à vos demandes de contact et de renseignements.</li>
        <li>Traiter vos demandes de services et de formations, et assurer le suivi des projets.</li>
        <li>Vous informer par e-mail de l’avancement de vos demandes et des nouveaux messages, selon les préférences choisies dans votre profil.</li>
        <li>Assurer la sécurité et le bon fonctionnement du site.</li>
    </ul>

    <h2 id="base-legale">Base légale</h2>
    <ul>
        <li><strong>Votre consentement</strong>, donné au moyen de la case à cocher du formulaire de contact.</li>
        <li><strong>L’exécution d’un contrat</strong>, lorsque vous faites appel à nos services ou à une formation.</li>
        <li><strong>Notre intérêt légitime</strong>, pour la sécurité du site et l’amélioration de nos services.</li>
    </ul>

    <h2 id="conservation">Durée de conservation</h2>
    <p>Les données transmises par le formulaire de contact sont conservées <strong>3 ans</strong> à compter de votre dernier échange avec AgriFish. Les données de l’espace client sont conservées pendant la durée de la relation, puis supprimées lorsque vous supprimez votre compte, sauf obligation légale de conservation.</p>

    <h2 id="partage">Partage des données</h2>
    <p>AgriFish ne vend pas vos données. Elles peuvent être transmises uniquement à nos prestataires techniques (hébergement, envoi d’e-mails), dans la limite de ce qui est nécessaire à leur mission et dans le respect des obligations légales.</p>

    <h2 id="droits">Vos droits</h2>
    <p>Conformément à la réglementation applicable, vous disposez d’un droit d’accès, de rectification, d’effacement et d’opposition sur vos données. Vous pouvez aussi modifier ou supprimer votre compte à tout moment depuis votre profil.</p>
    <p>Pour exercer vos droits, écrivez à <a href="mailto:{{ $legal['email_donnees'] }}">{{ $legal['email_donnees'] }}</a> en précisant l’adresse e-mail de votre compte. Nous répondons dans un délai d’un mois. Si la réponse ne vous satisfait pas, vous pouvez saisir l’autorité de protection des données de votre pays.</p>

    <h2 id="securite">Sécurité</h2>
    <p>Nous prenons des mesures techniques et organisationnelles pour protéger vos données contre la perte, l’accès non autorisé ou la divulgation. Les documents déposés dans l’espace client ne sont accessibles qu’au client concerné, à l’expert chargé du projet et aux administrateurs d’AgriFish.</p>

    <h2 id="contact">Contact</h2>
    <ul>
        <li><strong>E-mail :</strong> <a href="mailto:{{ $legal['email_donnees'] }}">{{ $legal['email_donnees'] }}</a></li>
        @if($legal['siege'])
            <li><strong>Courrier :</strong> {{ $legal['raison_sociale'] }}, protection des données, {{ $legal['siege'] }}</li>
        @endif
    </ul>
</x-legal-page>
@endsection
