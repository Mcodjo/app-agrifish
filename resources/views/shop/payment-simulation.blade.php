@extends('layouts.app')

@section('title', 'Paiement de test')
@section('noindex', true)

@section('content')
<section class="max-w-content px-site py-16">
    <div class="mx-auto max-w-md rounded-2xl border border-line bg-white p-6 text-center">
        <p class="text-sm font-medium text-amber-800">Environnement de test</p>
        <h1 class="mt-2 text-2xl font-semibold text-dark">Paiement de {{ money($payment->amount) }}</h1>
        <p class="mt-2 text-sm leading-6 text-muted">Commande {{ $payment->order->number }}. Cette page remplace la page du prestataire de paiement tant que FedaPay n’est pas configuré. Elle n’existe pas en production.</p>
        <div class="mt-6 grid gap-2">
            <form method="POST" action="{{ URL::temporarySignedRoute('shop.payment.simulate.confirm', now()->addMinutes(30), $payment) }}">
                @csrf
                <input type="hidden" name="outcome" value="approved">
                <button type="submit" class="btn-primary w-full">Simuler un paiement réussi</button>
            </form>
            <form method="POST" action="{{ URL::temporarySignedRoute('shop.payment.simulate.confirm', now()->addMinutes(30), $payment) }}">
                @csrf
                <input type="hidden" name="outcome" value="declined">
                <button type="submit" class="btn-outline w-full">Simuler un paiement refusé</button>
            </form>
        </div>
    </div>
</section>
@endsection
