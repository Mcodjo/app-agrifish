@extends('layouts.espace')

@section('title', 'Commande '.$order->number)

@section('content')
<x-espace.header :back="route('account.orders.index')" back-label="Mes commandes" :eyebrow="'Passée le '.$order->created_at->translatedFormat('d F Y')" :title="'Commande '.$order->number">
    <x-slot:actions>
        <x-status-badge :status="$order->status" :label="$order->statusLabel()" size="lg" />
        <form method="POST" action="{{ route('account.orders.reorder', $order) }}">
            @csrf
            <button type="submit" class="btn-outline">Recommander</button>
        </form>
    </x-slot:actions>
</x-espace.header>

<x-espace.body>
    @include('shop.partials.order-detail', ['order' => $order, 'reviewForm' => $order->status === 'completed' ? 'account.orders.review-form' : null])
</x-espace.body>
@endsection
