@extends('layouts.espace')

@section('title', 'Boutique')

@php $maxDay = max(1, $daily->max('amount')); @endphp

@section('content')
<x-espace.header title="Boutique" subtitle="Ventes, commandes à traiter et état du stock.">
    <x-slot:actions>
        <a href="{{ route('shop.index') }}" target="_blank" class="btn-outline">Voir la boutique</a>
        <a href="{{ route('shop-admin.products.create') }}" class="btn-primary">Nouveau produit</a>
    </x-slot:actions>
</x-espace.header>

<x-espace.body class="space-y-6">
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-espace.stat label="Ventes du jour" :value="money($kpis['today'])" />
        <x-espace.stat label="Ventes du mois" :value="money($kpis['month'])" :hint="now()->translatedFormat('F Y')" />
        <x-espace.stat label="Commandes payées ce mois" :value="$kpis['orders']" :href="route('shop-admin.orders.index')" />
        <x-espace.stat label="Panier moyen" :value="money($kpis['basket'])" />
    </div>

    <div class="grid items-start gap-6 lg:grid-cols-[1fr_22rem]">
        <div class="space-y-6">
            <x-espace.section title="Ventes des 14 derniers jours" description="Montants encaissés, remboursements déduits.">
                <div class="flex h-44 items-end gap-1.5" role="img" aria-label="Ventes quotidiennes des 14 derniers jours">
                    @foreach($daily as $day)
                        <div class="group relative flex h-full flex-1 flex-col justify-end">
                            <div class="rounded-t bg-primary-500/80 transition group-hover:bg-primary-800" style="height: {{ $day['amount'] ? max(3, round($day['amount'] / $maxDay * 100)) : 1 }}%"></div>
                            <span class="pointer-events-none absolute -top-7 left-1/2 hidden -translate-x-1/2 whitespace-nowrap rounded bg-dark px-2 py-0.5 text-[11px] text-white group-hover:block">{{ money($day['amount']) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-2 flex gap-1.5 text-[11px] text-muted">
                    @foreach($daily as $day)
                        <span class="flex-1 text-center">{{ $loop->index % 2 === 0 || $loop->last ? $day['date']->format('d/m') : '' }}</span>
                    @endforeach
                </div>
            </x-espace.section>

            <x-espace.section title="Commandes à traiter" flush>
                <x-slot:actions>
                    @if($awaitingPayment)<span class="text-sm text-muted">{{ $awaitingPayment }} en attente de paiement</span>@endif
                    <a href="{{ route('shop-admin.orders.index', ['statut' => 'a-traiter']) }}" class="text-sm text-muted hover:text-dark">Toutes</a>
                </x-slot:actions>
                @forelse($toProcess as $order)
                    <a href="{{ route('shop-admin.orders.show', $order) }}" class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-3 last:border-0 hover:bg-cream/40">
                        <div>
                            <p class="font-medium text-dark">{{ $order->number }} · {{ $order->customerName() }}</p>
                            <p class="text-sm text-muted">{{ $order->created_at->diffForHumans() }} · {{ $order->paymentMethodLabel() }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-medium text-dark">{{ money($order->total) }}</span>
                            <x-status-badge :status="$order->status" :label="$order->statusLabel()" />
                        </div>
                    </a>
                @empty
                    <p class="px-5 py-6 text-sm text-muted">Aucune commande à traiter.</p>
                @endforelse
            </x-espace.section>

            <x-espace.section title="Meilleures ventes du mois" flush>
                @if($topProducts->isEmpty())
                    <p class="px-5 py-6 text-sm text-muted">Pas encore de vente ce mois-ci.</p>
                @else
                    <table class="w-full text-sm">
                        <thead class="bg-cream/50 text-left text-xs text-muted"><tr><th class="px-5 py-2 font-medium">Produit</th><th class="px-5 py-2 text-right font-medium">Quantité</th><th class="px-5 py-2 text-right font-medium">Montant</th></tr></thead>
                        <tbody class="divide-y divide-line">
                            @foreach($topProducts as $row)
                                <tr><td class="px-5 py-2.5 text-dark">{{ $row->product_name }}</td><td class="px-5 py-2.5 text-right">{{ $row->quantity }}</td><td class="px-5 py-2.5 text-right">{{ money($row->revenue) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </x-espace.section>
        </div>

        <aside class="space-y-6">
            <x-espace.section title="Stock bas" flush>
                <x-slot:actions><a href="{{ route('shop-admin.products.index', ['etat' => 'stock-bas']) }}" class="text-sm text-muted hover:text-dark">Voir</a></x-slot:actions>
                @forelse($lowStock as $variant)
                    <a href="{{ route('shop-admin.products.edit', $variant->product) }}" class="flex items-center justify-between gap-3 border-b border-line px-5 py-2.5 text-sm last:border-0 hover:bg-cream/40">
                        <span class="min-w-0 truncate text-dark">{{ $variant->displayName() }}</span>
                        <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium {{ $variant->stock <= 0 ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-800' }}">{{ $variant->stock <= 0 ? 'Rupture' : $variant->stock.' restant(s)' }}</span>
                    </a>
                @empty
                    <p class="px-5 py-5 text-sm text-muted">Aucun produit sous son seuil d’alerte.</p>
                @endforelse
            </x-espace.section>

            <x-espace.section title="Raccourcis">
                <ul class="space-y-1 text-sm">
                    <li><a href="{{ route('shop-admin.reviews.index') }}" class="flex justify-between rounded-lg px-2 py-1.5 hover:bg-cream"><span>Avis à valider</span><span class="font-medium {{ $pendingReviews ? 'text-amber-800' : 'text-muted' }}">{{ $pendingReviews }}</span></a></li>
                    <li><a href="{{ route('shop-admin.coupons.create') }}" class="block rounded-lg px-2 py-1.5 hover:bg-cream">Créer un code promo</a></li>
                    <li><a href="{{ route('shop-admin.import-export') }}" class="block rounded-lg px-2 py-1.5 hover:bg-cream">Exporter les ventes</a></li>
                    @if(auth()->user()->isAdmin())
                        <li><a href="{{ route('admin.settings.edit') }}" class="block rounded-lg px-2 py-1.5 hover:bg-cream">Réglages de la boutique</a></li>
                    @endif
                </ul>
            </x-espace.section>
        </aside>
    </div>
</x-espace.body>
@endsection
