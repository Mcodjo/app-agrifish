@extends('layouts.app')

@section('title', 'Panier')
@section('noindex', true)

@section('content')
<section class="border-b border-line bg-cream/60">
    <div class="max-w-content px-site py-8">
        <h1 class="display-md">Votre panier</h1>
        <x-shop.checkout-steps :current="1" class="mt-5" />
    </div>
</section>

<section class="max-w-content px-site py-10">
    @if(session('reorder_missing'))
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Ces articles n’ont pas pu être ajoutés, faute de stock : {{ implode(', ', session('reorder_missing')) }}.
        </div>
    @endif

    @if($summary['count'] === 0)
        <div class="rounded-2xl border border-dashed border-line px-6 py-16 text-center">
            <p class="text-lg font-semibold text-dark">Votre panier est vide</p>
            <p class="mt-2 text-muted">Parcourez la boutique pour ajouter des produits.</p>
            <a href="{{ route('shop.index') }}" class="btn-primary mt-6">Voir la boutique</a>
        </div>
    @else
        <div class="grid items-start gap-8 lg:grid-cols-[1fr_22rem]">
            <div>
                @if($summary['problems'])
                    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                        <p class="font-medium">Certains articles doivent être ajustés avant de commander :</p>
                        <ul class="mt-1 list-disc pl-5">@foreach($summary['problems'] as $problem)<li>{{ $problem }}</li>@endforeach</ul>
                    </div>
                @endif

                <ul class="divide-y divide-line rounded-2xl border border-line bg-white">
                    @foreach($summary['lines'] as $line)
                        @php $product = $line['product']; $variant = $line['variant']; @endphp
                        <li class="flex gap-4 p-4 sm:p-5">
                            <a href="{{ $product && ! $product->trashed() ? route('shop.product', $product) : '#' }}" class="h-20 w-20 shrink-0 overflow-hidden rounded-lg bg-cream sm:h-24 sm:w-24">
                                @if($product?->coverUrl())<img src="{{ $product->coverUrl() }}" alt="" class="h-full w-full object-cover">@endif
                            </a>
                            <div class="flex min-w-0 flex-1 flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <p class="font-semibold text-dark">{{ $product?->name }}</p>
                                    @if($variant?->name)<p class="text-sm text-muted">{{ $variant->name }}</p>@endif
                                    <p class="mt-1 text-sm text-muted">
                                        {{ money($line['unit_price']) }}
                                        @if($line['unit_price'] < $line['regular_price'])<span class="line-through">{{ money($line['regular_price']) }}</span>@endif
                                    </p>
                                    @unless($line['sellable'])
                                        <p class="mt-1 text-sm font-medium text-red-700">{{ $variant && $variant->stock > 0 ? 'Il en reste '.$variant->stock.'.' : 'Indisponible' }}</p>
                                    @endunless
                                </div>
                                <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                                    <form method="POST" action="{{ route('shop.cart.update', $line['item']) }}" class="flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <label for="qty-{{ $line['item']->id }}" class="sr-only">Quantité</label>
                                        <input id="qty-{{ $line['item']->id }}" type="number" name="quantity" min="0" max="{{ max(1, $variant?->stock ?? 1) }}" value="{{ $line['quantity'] }}" class="field-input w-20 !py-1.5 text-center" onchange="this.form.submit()">
                                        <noscript><button type="submit" class="btn-outline !py-1.5">OK</button></noscript>
                                    </form>
                                    <p class="w-28 text-right font-semibold text-dark">{{ money($line['line_total']) }}</p>
                                    <form method="POST" action="{{ route('shop.cart.remove', $line['item']) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm text-muted underline-offset-2 hover:text-red-700 hover:underline">Retirer</button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('shop.index') }}" class="link-arrow mt-5 inline-flex">Continuer mes achats</a>
            </div>

            <aside class="space-y-4 lg:sticky lg:top-24">
                <div class="rounded-2xl border border-line bg-white p-5">
                    <h2 class="font-semibold text-dark">Récapitulatif</h2>
                    @include('shop.partials.totals', ['summary' => $summary])

                    @if($summary['coupon'])
                        <div class="mt-4 flex items-center justify-between rounded-lg bg-cream px-3 py-2 text-sm">
                            <span>Code <strong>{{ $summary['coupon']->code }}</strong> {{ $summary['coupon_problem'] ? '' : '('.$summary['coupon']->describe().')' }}</span>
                            <form method="POST" action="{{ route('shop.cart.coupon.remove') }}">@csrf @method('DELETE')<button class="text-muted hover:text-dark" aria-label="Retirer le code">Retirer</button></form>
                        </div>
                        @if($summary['coupon_problem'])<p class="field-error">{{ $summary['coupon_problem'] }}</p>@endif
                    @else
                        <form method="POST" action="{{ route('shop.cart.coupon') }}" class="mt-4 flex gap-2">
                            @csrf
                            <label for="code" class="sr-only">Code promo</label>
                            <input id="code" name="code" value="{{ old('code') }}" placeholder="Code promo" class="field-input !py-2 uppercase">
                            <button type="submit" class="btn-outline !py-2">Appliquer</button>
                        </form>
                        @error('code')<p class="field-error">{{ $message }}</p>@enderror
                    @endif

                    @if($summary['problems'])
                        <button type="button" disabled class="btn-primary mt-5 w-full cursor-not-allowed opacity-50">Passer la commande</button>
                    @else
                        <a href="{{ route('shop.checkout') }}" class="btn-primary mt-5 w-full">Passer la commande</a>
                    @endif
                </div>
                <p class="px-1 text-sm leading-6 text-muted">Paiement par Mobile Money, carte bancaire ou à la remise. La remise de la commande est organisée avec vous par notre équipe.</p>
            </aside>
        </div>
    @endif
</section>
@endsection
