@extends('layouts.app')

@section('title', 'Commande '.$order->number)
@section('noindex', true)

@section('content')
<section class="border-b border-line bg-cream/60">
    <div class="max-w-content px-site py-8">
        @if(in_array(session('status'), ['order-placed', 'payment-approved'], true))
            <x-shop.checkout-steps :current="3" class="mb-6" />
        @endif
        <div class="flex flex-wrap items-center gap-3">
            <h1 class="display-md">Commande {{ $order->number }}</h1>
            <x-status-badge :status="$order->status" :label="$order->statusLabel()" size="lg" />
        </div>
        <p class="lead mt-2">
            @switch($order->status)
                @case('pending_payment') Votre commande est enregistrée et attend son paiement. @break
                @case('new') Merci ! Votre commande est enregistrée : notre équipe vous contacte pour la remise. @break
                @case('paid') Merci ! Votre paiement est reçu et votre commande confirmée. @break
                @case('completed') Votre commande vous a été remise. Merci pour votre confiance. @break
                @default Suivez ici l’avancement de votre commande.
            @endswitch
        </p>
        <p class="mt-2 text-sm text-muted">Un e-mail récapitulatif a été envoyé à {{ $order->email }}. Gardez-le : il contient le lien de suivi de cette commande.</p>
    </div>
</section>

<section class="max-w-content px-site py-10">
    @include('shop.partials.order-detail', ['order' => $order])

    @guest
        <div class="mt-8 rounded-2xl bg-cream p-5 text-sm leading-6 text-dark sm:flex sm:items-center sm:justify-between sm:gap-6">
            <p>Créez un compte avec l’adresse {{ $order->email }} pour retrouver toutes vos commandes et vos factures au même endroit.</p>
            <a href="{{ route('register') }}" class="btn-secondary mt-3 shrink-0 sm:mt-0">Créer mon compte</a>
        </div>
    @endguest
</section>
@endsection
