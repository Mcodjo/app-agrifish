@php
    $flow = ['created' => 'Commande passée', 'paid' => $order->payment_method === 'on_delivery' ? 'Confirmée' : 'Payée', 'preparing' => 'En préparation', 'ready' => 'Prête', 'completed' => 'Remise'];
    $reached = match ($order->status) {
        'pending_payment' => 0, 'new' => 1, 'paid' => 1, 'preparing' => 2, 'ready' => 3, 'completed' => 4, default => -1,
    };
    $closed = in_array($order->status, ['cancelled', 'refunded'], true);
@endphp

<div class="grid items-start gap-6 lg:grid-cols-[1fr_20rem]">
    <div class="space-y-6">
        @if($order->status === 'pending_payment')
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <h2 class="font-semibold text-amber-900">Paiement en attente</h2>
                <p class="mt-1 text-sm leading-6 text-amber-900/90">
                    Votre commande est réservée{{ $order->expires_at ? ' jusqu’au '.$order->expires_at->translatedFormat('d F à H:i') : '' }}. Sans paiement d’ici là, elle sera annulée automatiquement.
                </p>
                @if($order->canRetryPayment())
                    <form method="POST" action="{{ route('shop.orders.pay', $order) }}" class="mt-4">
                        @csrf
                        <button type="submit" class="btn-primary">Payer {{ money($order->total) }}</button>
                    </form>
                @endif
            </div>
        @elseif($order->payment_status === 'to_collect' && ! $closed)
            <div class="rounded-2xl border border-line bg-cream p-5 text-sm leading-6 text-dark">
                <p class="font-semibold">À régler à la remise : {{ money($order->total) }}</p>
                <p class="mt-1 text-muted">{{ \App\Models\Setting::get('pickup_instructions') }}</p>
            </div>
        @endif

        <div class="rounded-2xl border border-line bg-white p-5">
            <h2 class="font-semibold text-dark">Suivi</h2>
            @if($closed)
                <p class="mt-3 text-sm text-muted">Cette commande est {{ mb_strtolower($order->statusLabel()) }}{{ $order->cancelled_at ? ' depuis le '.$order->cancelled_at->translatedFormat('d F Y') : '' }}.</p>
            @else
                <ol class="mt-5 grid gap-4 sm:grid-cols-5">
                    @foreach(array_values($flow) as $index => $label)
                        <li class="flex items-center gap-3 sm:flex-col sm:items-start sm:gap-2">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-semibold {{ $index <= $reached ? 'bg-primary-800 text-white' : 'bg-cream text-muted' }}">
                                @if($index <= $reached)
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </span>
                            <span class="text-sm {{ $index <= $reached ? 'font-medium text-dark' : 'text-muted' }}">{{ $label }}</span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-line bg-white">
            <h2 class="border-b border-line px-5 py-4 font-semibold text-dark">Articles</h2>
            <ul class="divide-y divide-line">
                @foreach($order->items as $item)
                    <li class="flex gap-4 px-5 py-4">
                        <span class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-cream">
                            @if($item->product?->coverUrl())<img src="{{ $item->product->coverUrl() }}" alt="" class="h-full w-full object-cover">@endif
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-dark">{{ $item->product_name }}</p>
                            <p class="text-sm text-muted">{{ $item->variant_name ? $item->variant_name.' · ' : '' }}{{ $item->quantity }} × {{ money($item->unit_price) }}</p>
                            {{ $itemActions ?? '' }}
                            @isset($reviewForm)
                                @include($reviewForm, ['item' => $item])
                            @endisset
                        </div>
                        <p class="font-medium text-dark">{{ money($item->line_total) }}</p>
                    </li>
                @endforeach
            </ul>
            <dl class="space-y-2 border-t border-line px-5 py-4 text-sm">
                <div class="flex justify-between"><dt class="text-muted">Sous-total</dt><dd>{{ money($order->subtotal) }}</dd></div>
                @if($order->discount)<div class="flex justify-between text-primary-800"><dt>Réduction ({{ $order->coupon_code }})</dt><dd>-{{ money($order->discount) }}</dd></div>@endif
                <div class="flex justify-between text-base font-semibold text-dark"><dt>Total TTC</dt><dd>{{ money($order->total) }}</dd></div>
                @if($order->refunded_amount)<div class="flex justify-between text-muted"><dt>Remboursé</dt><dd>-{{ money($order->refunded_amount) }}</dd></div>@endif
            </dl>
        </div>
    </div>

    <aside class="space-y-4">
        <div class="rounded-2xl border border-line bg-white p-5 text-sm">
            <h2 class="font-semibold text-dark">Informations</h2>
            <dl class="mt-3 space-y-3">
                <div><dt class="text-muted">Date</dt><dd class="text-dark">{{ $order->created_at->translatedFormat('d F Y à H:i') }}</dd></div>
                <div><dt class="text-muted">Paiement</dt><dd class="text-dark">{{ $order->paymentMethodLabel() }} · {{ $order->paymentStatusLabel() }}</dd></div>
                <div><dt class="text-muted">Coordonnées</dt><dd class="text-dark">{{ $order->customerName() }}<br>{{ $order->phone }}<br>{{ $order->email }}</dd></div>
                @if($order->addressLine())<div><dt class="text-muted">Adresse</dt><dd class="text-dark">{{ $order->addressLine() }}</dd></div>@endif
                @if($order->customer_note)<div><dt class="text-muted">Vos précisions</dt><dd class="whitespace-pre-line text-dark">{{ $order->customer_note }}</dd></div>@endif
            </dl>
            @if($order->hasInvoice())
                <a href="{{ route('shop.orders.invoice', $order) }}" class="btn-outline mt-5 w-full">Télécharger la facture (PDF)</a>
            @endif
            {{ $asideActions ?? '' }}
        </div>
        <p class="px-1 text-sm leading-6 text-muted">Une question sur cette commande ? Écrivez-nous à <a href="mailto:{{ config('site.contact.email') }}" class="text-dark underline underline-offset-2">{{ config('site.contact.email') }}</a> en indiquant son numéro.</p>
    </aside>
</div>
