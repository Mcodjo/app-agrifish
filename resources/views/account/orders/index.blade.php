@extends('layouts.espace')

@section('title', 'Mes commandes')

@section('content')
<x-espace.header title="Mes commandes" subtitle="Suivez vos commandes, téléchargez vos factures et recommandez en un clic.">
    <x-slot:actions>
        <a href="{{ route('shop.index') }}" class="btn-primary">Aller à la boutique</a>
    </x-slot:actions>
</x-espace.header>

<x-espace.body class="space-y-5">
    <x-espace.filters :tabs="['' => 'Toutes', 'en-cours' => 'En cours', 'terminees' => 'Remises', 'annulees' => 'Annulées']" :current="$filter" />

    @if($orders->isEmpty())
        <x-espace.empty :title="$filter ? 'Aucune commande dans cette catégorie' : 'Aucune commande pour l’instant'" text="Vos commandes passées sur la boutique apparaîtront ici, avec leur suivi et leur facture.">
            <a href="{{ route('shop.index') }}" class="btn-primary">Découvrir la boutique</a>
        </x-espace.empty>
    @else
        <div class="overflow-hidden rounded-xl border border-line bg-white">
            <div class="hidden grid-cols-[1fr_9rem_8rem_10rem] gap-4 border-b border-line bg-cream/50 px-5 py-2.5 text-xs font-medium text-muted md:grid">
                <span>Commande</span><span>Montant</span><span>Paiement</span><span>Statut</span>
            </div>
            <ul class="divide-y divide-line">
                @foreach($orders as $order)
                    <li>
                        <a href="{{ route('account.orders.show', $order) }}" class="grid gap-2 px-5 py-4 hover:bg-cream/40 md:grid-cols-[1fr_9rem_8rem_10rem] md:items-center md:gap-4">
                            <span>
                                <span class="block font-medium text-dark">{{ $order->number }}</span>
                                <span class="block text-sm text-muted">{{ $order->created_at->translatedFormat('d F Y') }} · {{ $order->items_count }} {{ $order->items_count > 1 ? 'articles' : 'article' }}</span>
                            </span>
                            <span class="font-medium text-dark">{{ money($order->total) }}</span>
                            <span class="text-sm text-muted">{{ $order->paymentStatusLabel() }}</span>
                            <span><x-status-badge :status="$order->status" :label="$order->statusLabel()" /></span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div>{{ $orders->links() }}</div>
    @endif
</x-espace.body>
@endsection
