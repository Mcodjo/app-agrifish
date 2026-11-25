@extends('layouts.app')

@section('title', 'Conditions générales de vente')
@section('meta_description', 'Conditions générales de vente de la boutique en ligne AgriFish : commande, prix, paiement, remise, rétractation, garanties et réclamations.')

@php $legal = config('site.legal'); $contact = config('site.contact'); @endphp

@section('content')
<x-legal-page title="Conditions générales de vente" :updated="$legal['mise_a_jour']"
              intro="Ces conditions s’appliquent à toute commande passée sur la boutique en ligne AgriFish. Passer commande vaut acceptation de ces conditions."
              :sections="[
                  'vendeur' => 'Vendeur',
                  'produits' => 'Produits',
                  'prix' => 'Prix',
                  'commande' => 'Commande',
                  'paiement' => 'Paiement',
                  'remise' => 'Remise de la commande',
                  'retours' => 'Retours et remboursements',
                  'garanties' => 'Garanties et responsabilité',
                  'donnees' => 'Données personnelles',
                  'litiges' => 'Réclamations et litiges',
              ]">
    <h2 id="vendeur">Vendeur</h2>
    <p>La boutique est exploitée par {{ $legal['raison_sociale'] }}{{ $legal['forme'] ? ', '.$legal['forme'] : '' }}{{ $legal['siege'] ? ', dont le siège est situé '.$legal['siege'] : '' }}{{ $legal['immatriculation'] ? ', immatriculée sous le numéro '.$legal['immatriculation'] : '' }}. Contact : <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a>{{ $contact['telephone'] ? ', '.$contact['telephone'] : '' }}.</p>

    <h2 id="produits">Produits</h2>
    <p>Les produits proposés sont décrits avec le plus de précision possible : photos, caractéristiques, formats. Les photos sont non contractuelles. Les produits sont proposés dans la limite des stocks disponibles, affichés sur chaque fiche.</p>

    <h2 id="prix">Prix</h2>
    <p>Les prix sont indiqués en francs CFA (XOF), toutes taxes comprises. Le prix applicable est celui affiché au moment de la validation de la commande. Les éventuelles promotions s’appliquent pendant la période indiquée.</p>

    <h2 id="commande">Commande</h2>
    <p>Le client choisit ses produits, vérifie le récapitulatif de son panier, renseigne ses coordonnées, choisit son mode de paiement et accepte les présentes conditions. La commande est enregistrée à sa validation et un e-mail récapitulatif est envoyé au client. Les articles commandés sont réservés pendant le délai de paiement.</p>

    <h2 id="paiement">Paiement</h2>
    <p>Le paiement s’effectue selon le mode choisi : en ligne par Mobile Money ou carte bancaire, via un prestataire de paiement agréé, ou à la remise de la commande lorsque cette option est proposée. AgriFish ne conserve aucune donnée bancaire. Une commande payable en ligne et non réglée dans le délai indiqué dans l’e-mail de confirmation est annulée automatiquement.</p>

    <h2 id="remise">Remise de la commande</h2>
    <p>La remise de la commande est organisée avec le client par l’équipe AgriFish, à partir des coordonnées communiquées lors de la commande. Le client est informé par e-mail lorsque sa commande est prête. Il vérifie l’état des produits au moment de la remise et signale immédiatement toute anomalie.</p>

    <h2 id="retours">Retours et remboursements</h2>
    <p>Le client peut demander l’annulation d’une commande tant qu’elle n’est pas en préparation, en écrivant à <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a> avec le numéro de commande. Un produit non conforme ou endommagé est repris ou échangé après vérification. Le remboursement, total ou partiel, est effectué sur le moyen de paiement utilisé. Les produits périssables, vivants ou entamés ne peuvent pas être repris, sauf défaut constaté à la remise.</p>

    <h2 id="garanties">Garanties et responsabilité</h2>
    <p>Les produits bénéficient des garanties légales applicables. AgriFish ne peut être tenue responsable d’un usage des produits non conforme à leurs indications ou à leur fiche technique.</p>

    <h2 id="donnees">Données personnelles</h2>
    <p>Les données collectées lors de la commande servent à la traiter, à remettre les produits et à établir la facture. Leur traitement est décrit dans la <a href="{{ route('politique-confidentialite') }}">politique de confidentialité</a>.</p>

    <h2 id="litiges">Réclamations et litiges</h2>
    <p>Toute réclamation est adressée à <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a>. Une solution amiable est recherchée en priorité. À défaut, le litige relève des juridictions compétentes du ressort du siège du vendeur, selon le droit applicable dans ce pays.</p>
</x-legal-page>
@endsection
