@php
    $fcfa = fn ($amount) => number_format((int) $amount, 0, ',', ' ').' FCFA';
    $invoiceNumber = 'F-'.$order->number;
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Facture {{ $invoiceNumber }}</title>
<style>
    * { font-family: 'DejaVu Sans', sans-serif; }
    body { color: #17211b; font-size: 11px; margin: 0; }
    .top { width: 100%; border-bottom: 2px solid #1b4332; padding-bottom: 14px; }
    .brand { font-size: 22px; font-weight: bold; color: #1b4332; }
    .brand span { color: #b8862b; }
    .muted { color: #5c665f; }
    h1 { font-size: 18px; margin: 0 0 4px; }
    table { width: 100%; border-collapse: collapse; }
    .cols td { vertical-align: top; width: 50%; padding-top: 18px; }
    .items { margin-top: 22px; }
    .items th { background: #1b4332; color: #fff; text-align: left; padding: 7px 8px; font-size: 10px; }
    .items td { padding: 7px 8px; border-bottom: 1px solid #e4e0d6; }
    .right { text-align: right; }
    .totals { width: 45%; margin-left: 55%; margin-top: 14px; }
    .totals td { padding: 4px 8px; }
    .grand td { border-top: 2px solid #1b4332; font-weight: bold; font-size: 13px; padding-top: 8px; }
    .foot { position: fixed; bottom: 0; left: 0; right: 0; font-size: 9px; color: #5c665f; border-top: 1px solid #e4e0d6; padding-top: 6px; text-align: center; }
</style>
</head>
<body>
<table class="top">
    <tr>
        <td><div class="brand">Agri<span>Fish</span></div><div class="muted">{{ $legal['raison_sociale'] }}</div></td>
        <td class="right">
            <h1>Facture</h1>
            <div>N° {{ $invoiceNumber }}</div>
            <div class="muted">Date : {{ ($order->paid_at ?? $order->completed_at ?? $order->created_at)->format('d/m/Y') }}</div>
            <div class="muted">Commande {{ $order->number }} du {{ $order->created_at->format('d/m/Y') }}</div>
        </td>
    </tr>
</table>

<table class="cols">
    <tr>
        <td>
            <strong>Vendeur</strong><br>
            {{ $legal['raison_sociale'] }}{{ $legal['forme'] ? ', '.$legal['forme'] : '' }}<br>
            @if($legal['siege']){{ $legal['siege'] }}<br>@endif
            @if($legal['immatriculation'])RCCM : {{ $legal['immatriculation'] }}<br>@endif
            @if($legal['identifiant_fiscal'])IFU : {{ $legal['identifiant_fiscal'] }}<br>@endif
            {{ $contact['email'] }}@if($contact['telephone']) · {{ $contact['telephone'] }}@endif
        </td>
        <td>
            <strong>Client</strong><br>
            {{ $order->customerName() }}<br>
            @if($order->addressLine()){{ $order->addressLine() }}<br>@endif
            {{ $order->phone }}<br>
            {{ $order->email }}
        </td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr><th>Désignation</th><th>Référence</th><th class="right">Qté</th><th class="right">Prix unitaire TTC</th><th class="right">Total TTC</th></tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>{{ $item->label() }}</td>
                <td>{{ $item->sku }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">{{ $fcfa($item->unit_price) }}</td>
                <td class="right">{{ $fcfa($item->line_total) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="totals">
    <tr><td>Sous-total</td><td class="right">{{ $fcfa($order->subtotal) }}</td></tr>
    @if($order->discount)<tr><td>Réduction ({{ $order->coupon_code }})</td><td class="right">-{{ $fcfa($order->discount) }}</td></tr>@endif
    <tr class="grand"><td>Total TTC</td><td class="right">{{ $fcfa($order->total) }}</td></tr>
    @if($order->refunded_amount)<tr><td>Remboursé</td><td class="right">-{{ $fcfa($order->refunded_amount) }}</td></tr>@endif
    <tr><td class="muted">Paiement</td><td class="right muted">{{ $order->paymentMethodLabel() }} · {{ $order->paymentStatusLabel() }}</td></tr>
</table>

<div class="foot">{{ $legal['raison_sociale'] }} · {{ $contact['email'] }} · Facture établie à partir de la commande {{ $order->number }}</div>
</body>
</html>
