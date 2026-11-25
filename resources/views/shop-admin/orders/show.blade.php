@extends('layouts.espace')

@section('title', 'Commande '.$order->number)

@php
    $next = $order->nextStatus();
    $nextLabels = ['preparing' => 'Passer en préparation', 'ready' => 'Marquer comme prête', 'completed' => 'Marquer comme remise'];
@endphp

@section('content')
<x-espace.header :back="route('shop-admin.orders.index')" back-label="Commandes" :eyebrow="'Passée le '.$order->created_at->translatedFormat('d F Y à H:i')" :title="'Commande '.$order->number">
    <x-slot:actions>
        <x-status-badge :status="$order->status" :label="$order->statusLabel()" size="lg" />
        @if($order->hasInvoice())
            <a href="{{ route('shop.orders.invoice', $order) }}" class="btn-outline">Facture</a>
        @endif
    </x-slot:actions>
</x-espace.header>

<x-espace.body>
    <div class="grid items-start gap-6 lg:grid-cols-[1fr_21rem]">
        <div class="space-y-6">
            @if($next || $order->payment_status === 'to_collect')
                <div class="rounded-xl border border-primary-200 bg-primary-50/60 p-5">
                    <h2 class="font-semibold text-dark">Prochaine action</h2>
                    <p class="mt-1 text-sm text-muted">
                        @switch($order->status)
                            @case('new') Commande à encaisser à la remise : préparez-la puis contactez le client au {{ $order->phone }}. @break
                            @case('paid') Paiement reçu : préparez la commande. @break
                            @case('preparing') Une fois la commande prête, le client est prévenu par e-mail. @break
                            @case('ready') Contactez le client pour la remise, puis marquez la commande comme remise. @break
                        @endswitch
                    </p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @if($next)
                            <form method="POST" action="{{ route('shop-admin.orders.advance', $order) }}" class="flex flex-wrap items-center gap-3">
                                @csrf
                                @if($next === 'completed' && $order->payment_status === 'to_collect')
                                    <label class="flex items-center gap-2 text-sm text-dark"><input type="checkbox" name="collected" value="1" checked class="h-4 w-4 rounded border-line text-primary-800"> Paiement de {{ money($order->total) }} encaissé</label>
                                @endif
                                <button type="submit" class="btn-primary">{{ $nextLabels[$next] }}</button>
                            </form>
                        @endif
                        @if($order->payment_status === 'to_collect' && $next !== 'completed')
                            <form method="POST" action="{{ route('shop-admin.orders.collect', $order) }}">
                                @csrf
                                <button type="submit" class="btn-outline">Enregistrer le paiement encaissé</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            <x-espace.section title="Articles" flush>
                <table class="w-full text-sm">
                    <thead class="bg-cream/50 text-left text-xs text-muted"><tr><th class="px-5 py-2 font-medium">Produit</th><th class="px-5 py-2 text-right font-medium">Qté</th><th class="px-5 py-2 text-right font-medium">Prix</th><th class="px-5 py-2 text-right font-medium">Total</th></tr></thead>
                    <tbody class="divide-y divide-line">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-5 py-3">
                                    <p class="font-medium text-dark">{{ $item->product_name }}</p>
                                    <p class="text-xs text-muted">{{ $item->variant_name ? $item->variant_name.' · ' : '' }}{{ $item->sku }}</p>
                                </td>
                                <td class="px-5 py-3 text-right">{{ $item->quantity }}</td>
                                <td class="px-5 py-3 text-right">{{ money($item->unit_price) }}</td>
                                <td class="px-5 py-3 text-right font-medium">{{ money($item->line_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t border-line text-sm">
                        <tr><td colspan="3" class="px-5 pt-3 text-right text-muted">Sous-total</td><td class="px-5 pt-3 text-right">{{ money($order->subtotal) }}</td></tr>
                        @if($order->discount)<tr><td colspan="3" class="px-5 pt-1 text-right text-muted">Réduction ({{ $order->coupon_code }})</td><td class="px-5 pt-1 text-right">-{{ money($order->discount) }}</td></tr>@endif
                        <tr><td colspan="3" class="px-5 py-3 text-right font-semibold text-dark">Total TTC</td><td class="px-5 py-3 text-right font-semibold text-dark">{{ money($order->total) }}</td></tr>
                        @if($order->refunded_amount)<tr><td colspan="3" class="px-5 pb-3 text-right text-muted">Remboursé</td><td class="px-5 pb-3 text-right">-{{ money($order->refunded_amount) }}</td></tr>@endif
                    </tfoot>
                </table>
            </x-espace.section>

            <x-espace.section title="Historique">
                <ol class="space-y-4">
                    @foreach($order->events as $event)
                        <li class="flex gap-3 text-sm">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ in_array($event->type, ['cancelled', 'expired', 'payment_failed'], true) ? 'bg-red-500' : (in_array($event->type, ['paid', 'collected'], true) ? 'bg-primary-500' : 'bg-gold') }}"></span>
                            <div>
                                <p class="text-dark">{{ $event->summary() }}</p>
                                <p class="text-xs text-muted">{{ $event->created_at->translatedFormat('d M Y à H:i') }}{{ $event->user ? ' · '.$event->user->name : '' }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
                <form method="POST" action="{{ route('shop-admin.orders.note', $order) }}" class="mt-5 flex gap-2 border-t border-line pt-4">
                    @csrf
                    <label for="note" class="sr-only">Ajouter une note interne</label>
                    <input id="note" name="note" maxlength="500" required placeholder="Ajouter une note interne (non visible par le client)" class="field-input !py-2 text-sm">
                    <button type="submit" class="btn-outline !py-2">Ajouter</button>
                </form>
            </x-espace.section>
        </div>

        <aside class="space-y-6">
            <x-espace.section title="Client">
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-muted">Nom</dt><dd class="text-dark">{{ $order->customerName() }}{{ $order->user ? '' : ' (sans compte)' }}</dd></div>
                    <div><dt class="text-muted">Téléphone</dt><dd><a href="tel:{{ preg_replace('/[^0-9+]/', '', $order->phone) }}" class="text-dark underline underline-offset-2">{{ $order->phone }}</a></dd></div>
                    <div><dt class="text-muted">E-mail</dt><dd><a href="mailto:{{ $order->email }}" class="break-all text-dark underline underline-offset-2">{{ $order->email }}</a></dd></div>
                    <div><dt class="text-muted">Adresse</dt><dd class="text-dark">{{ $order->addressLine() }}</dd></div>
                    @if($order->customer_note)<div><dt class="text-muted">Précisions du client</dt><dd class="whitespace-pre-line text-dark">{{ $order->customer_note }}</dd></div>@endif
                </dl>
            </x-espace.section>

            <x-espace.section title="Paiement">
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-muted">Mode</dt><dd class="text-right text-dark">{{ $order->paymentMethodLabel() }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-muted">État</dt><dd class="text-right text-dark">{{ $order->paymentStatusLabel() }}</dd></div>
                    @if($order->paid_at)<div class="flex justify-between gap-3"><dt class="text-muted">Payée le</dt><dd class="text-right text-dark">{{ $order->paid_at->format('d/m/Y H:i') }}</dd></div>@endif
                    @if($order->expires_at && $order->status === 'pending_payment')<div class="flex justify-between gap-3"><dt class="text-muted">Annulée si impayée le</dt><dd class="text-right text-dark">{{ $order->expires_at->format('d/m/Y H:i') }}</dd></div>@endif
                </dl>
                @if($order->payments->isNotEmpty())
                    <ul class="mt-4 space-y-2 border-t border-line pt-3 text-xs text-muted">
                        @foreach($order->payments as $payment)
                            <li class="flex justify-between gap-2"><span>{{ $payment->providerLabel() }} · {{ $payment->created_at->format('d/m H:i') }}</span><span class="text-dark">{{ $payment->statusLabel() }}</span></li>
                        @endforeach
                    </ul>
                @endif
            </x-espace.section>

            @if($order->refundableAmount() > 0)
                <x-espace.section title="Rembourser" x-data="{ open: {{ $errors->has('amount') ? 'true' : 'false' }} }">
                    <p class="text-sm leading-6 text-muted">Effectuez d’abord le remboursement chez le prestataire de paiement (ou en espèces), puis enregistrez-le ici. Le client est prévenu par e-mail.</p>
                    <form method="POST" action="{{ route('shop-admin.orders.refund', $order) }}" class="mt-3 space-y-3">
                        @csrf
                        <div>
                            <label for="amount" class="field-label">Montant (FCFA)</label>
                            <input id="amount" type="number" name="amount" min="1" max="{{ $order->refundableAmount() }}" value="{{ old('amount', $order->refundableAmount()) }}" required class="field-input">
                            @error('amount')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="refund-reason" class="field-label">Motif</label>
                            <input id="refund-reason" name="reason" maxlength="500" class="field-input" placeholder="Ex : produit endommagé">
                        </div>
                        @if($order->status !== 'cancelled')
                            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="restock" value="1" class="h-4 w-4 rounded border-line text-primary-800"> Remettre les articles en stock</label>
                        @endif
                        <button type="submit" class="btn-outline w-full" onclick="return confirm('Enregistrer ce remboursement ?')">Enregistrer le remboursement</button>
                    </form>
                </x-espace.section>
            @endif

            @if($order->isOpen())
                <x-espace.section title="Annuler la commande">
                    <form method="POST" action="{{ route('shop-admin.orders.cancel', $order) }}" class="space-y-3" onsubmit="return confirm('Annuler cette commande ? Le stock sera remis à jour et le client prévenu.')">
                        @csrf
                        <label for="cancel-reason" class="field-label">Motif communiqué au client</label>
                        <input id="cancel-reason" name="reason" maxlength="500" class="field-input" placeholder="Facultatif">
                        <button type="submit" class="btn w-full border border-red-200 bg-white text-red-700 hover:bg-red-50">Annuler la commande</button>
                        @if($order->isPaid())<p class="text-xs text-muted">La commande est payée : pensez à enregistrer le remboursement.</p>@endif
                    </form>
                </x-espace.section>
            @endif
        </aside>
    </div>
</x-espace.body>
@endsection
