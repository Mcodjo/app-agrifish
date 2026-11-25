@extends('layouts.espace')

@section('title', 'Avis clients')

@section('content')
<x-espace.header title="Avis clients" subtitle="Seuls les clients qui ont reçu un produit peuvent le noter. Un avis n’est publié qu’après validation." />

<x-espace.body class="space-y-4">
    <x-espace.filters :tabs="collect(\App\Models\Shop\ProductReview::STATUSES)->map(fn ($label, $key) => [$label, $counts[$key] ?? 0])->all()" :current="$status" param="statut" />

    @forelse($reviews as $review)
        <article class="rounded-xl border border-line bg-white p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <div class="flex items-center gap-3"><x-shop.stars :value="$review->rating" /><span class="text-sm font-medium text-dark">{{ $review->user?->name }}</span></div>
                    <p class="mt-1 text-sm text-muted">{{ $review->product?->name }} · {{ $review->created_at->translatedFormat('d F Y') }}</p>
                </div>
                <div class="flex gap-2">
                    @if($review->status !== 'approved')
                        <form method="POST" action="{{ route('shop-admin.reviews.update', $review) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="approved"><button class="btn-primary !py-1.5 text-sm">Publier</button></form>
                    @endif
                    @if($review->status !== 'rejected')
                        <form method="POST" action="{{ route('shop-admin.reviews.update', $review) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="rejected"><button class="btn-outline !py-1.5 text-sm">Refuser</button></form>
                    @endif
                    <form method="POST" action="{{ route('shop-admin.reviews.destroy', $review) }}" onsubmit="return confirm('Supprimer cet avis ?')">@csrf @method('DELETE')<button class="btn-outline !py-1.5 text-sm text-red-700">Supprimer</button></form>
                </div>
            </div>
            @if($review->comment)<p class="mt-3 whitespace-pre-line text-[15px] leading-7 text-dark">{{ $review->comment }}</p>@endif
        </article>
    @empty
        <x-espace.empty title="Aucun avis dans cette catégorie" />
    @endforelse
    <div>{{ $reviews->links() }}</div>
</x-espace.body>
@endsection
