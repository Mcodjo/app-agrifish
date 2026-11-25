@extends('layouts.espace')

@section('title', 'Commandes')

@php
    $toProcess = collect(['new', 'paid', 'preparing', 'ready'])->sum(fn ($s) => $counts[$s] ?? 0);
    $tabs = ['' => ['Toutes', $counts->sum()], 'a-traiter' => ['À traiter', $toProcess], 'pending_payment' => ['Attente paiement', $counts['pending_payment'] ?? 0], 'completed' => ['Remises', $counts['completed'] ?? 0], 'cancelled' => ['Annulées', $counts['cancelled'] ?? 0], 'refunded' => ['Remboursées', $counts['refunded'] ?? 0]];
    $keep = array_filter(['q' => $search, 'paiement' => $method, 'du' => $from, 'au' => $to]);
@endphp

@section('content')
<x-espace.header title="Commandes" subtitle="Les commandes payées et celles à encaisser à la remise apparaissent en premier." />

<x-espace.body class="space-y-4">
    <x-espace.filters :tabs="$tabs" :current="$status" param="statut" :keep="$keep" />

    <form method="GET" class="grid gap-3 rounded-xl border border-line bg-white p-4 md:grid-cols-[1fr_12rem_9rem_9rem_auto]">
        @if($status)<input type="hidden" name="statut" value="{{ $status }}">@endif
        <div>
            <label for="q" class="sr-only">Rechercher</label>
            <input id="q" type="search" name="q" value="{{ $search }}" placeholder="Numéro, nom, e-mail ou téléphone" class="field-input !py-2 text-sm">
        </div>
        <div>
            <label for="paiement" class="sr-only">Mode de paiement</label>
            <select id="paiement" name="paiement" class="field-input !py-2 text-sm">
                <option value="">Tous les paiements</option>
                @foreach(\App\Models\Shop\Order::PAYMENT_METHODS as $value => $label)
                    <option value="{{ $value }}" @selected($method === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div><label for="du" class="sr-only">Du</label><input id="du" type="date" name="du" value="{{ $from }}" class="field-input !py-2 text-sm" title="Du"></div>
        <div><label for="au" class="sr-only">Au</label><input id="au" type="date" name="au" value="{{ $to }}" class="field-input !py-2 text-sm" title="Au"></div>
        <button type="submit" class="btn-primary !py-2">Filtrer</button>
    </form>

    @if($orders->isEmpty())
        <x-espace.empty title="Aucune commande" text="Aucune commande ne correspond à ces critères." />
    @else
        <div class="overflow-x-auto rounded-xl border border-line bg-white">
            <table class="w-full min-w-[720px] text-sm">
                <thead class="border-b border-line bg-cream/50 text-left text-xs text-muted">
                    <tr>
                        <th class="px-4 py-2.5 font-medium">Commande</th>
                        <th class="px-4 py-2.5 font-medium">Client</th>
                        <th class="px-4 py-2.5 font-medium">Paiement</th>
                        <th class="px-4 py-2.5 text-right font-medium">Total</th>
                        <th class="px-4 py-2.5 font-medium">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach($orders as $order)
                        <tr class="cursor-pointer hover:bg-cream/40" onclick="window.location='{{ route('shop-admin.orders.show', $order) }}'">
                            <td class="px-4 py-3">
                                <a href="{{ route('shop-admin.orders.show', $order) }}" class="font-medium text-dark hover:underline">{{ $order->number }}</a>
                                <p class="text-xs text-muted">{{ $order->created_at->format('d/m/Y H:i') }} · {{ $order->items_count }} art.</p>
                            </td>
                            <td class="px-4 py-3"><p class="text-dark">{{ $order->customerName() }}</p><p class="text-xs text-muted">{{ $order->phone }} · {{ $order->city }}</p></td>
                            <td class="px-4 py-3"><p class="text-dark">{{ $order->paymentMethodLabel() }}</p><p class="text-xs text-muted">{{ $order->paymentStatusLabel() }}</p></td>
                            <td class="px-4 py-3 text-right font-medium text-dark">{{ money($order->total) }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$order->status" :label="$order->statusLabel()" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>{{ $orders->links() }}</div>
    @endif
</x-espace.body>
@endsection
