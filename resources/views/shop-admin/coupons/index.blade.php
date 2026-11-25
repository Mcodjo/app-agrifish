@extends('layouts.espace')

@section('title', 'Codes promo')

@section('content')
<x-espace.header title="Codes promo" subtitle="Réductions en pourcentage ou en montant, avec dates et nombre d’utilisations.">
    <x-slot:actions><a href="{{ route('shop-admin.coupons.create') }}" class="btn-primary">Nouveau code</a></x-slot:actions>
</x-espace.header>

<x-espace.body>
    @if($coupons->isEmpty())
        <x-espace.empty title="Aucun code promo" text="Créez un code à communiquer à vos clients.">
            <a href="{{ route('shop-admin.coupons.create') }}" class="btn-primary">Nouveau code</a>
        </x-espace.empty>
    @else
        <div class="overflow-x-auto rounded-xl border border-line bg-white">
            <table class="w-full min-w-[680px] text-sm">
                <thead class="border-b border-line bg-cream/50 text-left text-xs text-muted"><tr><th class="px-4 py-2.5 font-medium">Code</th><th class="px-4 py-2.5 font-medium">Réduction</th><th class="px-4 py-2.5 font-medium">Validité</th><th class="px-4 py-2.5 font-medium">Utilisations</th><th class="px-4 py-2.5 font-medium">État</th><th class="px-4 py-2.5"></th></tr></thead>
                <tbody class="divide-y divide-line">
                    @foreach($coupons as $coupon)
                        @php $problem = $coupon->problemFor(PHP_INT_MAX); @endphp
                        <tr>
                            <td class="px-4 py-3 font-mono font-medium text-dark">{{ $coupon->code }}</td>
                            <td class="px-4 py-3">{{ $coupon->describe() }}@if($coupon->min_amount)<span class="block text-xs text-muted">dès {{ money($coupon->min_amount) }}</span>@endif</td>
                            <td class="px-4 py-3 text-muted">{{ $coupon->starts_at?->format('d/m/Y') ?? '—' }} → {{ $coupon->ends_at?->format('d/m/Y') ?? 'sans fin' }}</td>
                            <td class="px-4 py-3">{{ $coupon->used_count }}{{ $coupon->max_uses ? ' / '.$coupon->max_uses : '' }}</td>
                            <td class="px-4 py-3">
                                @if($problem)<span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-600" title="{{ $problem }}">Inactif</span>
                                @else<span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Actif</span>@endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('shop-admin.coupons.edit', $coupon) }}" class="font-medium text-primary-800">Modifier</a>
                                <form method="POST" action="{{ route('shop-admin.coupons.destroy', $coupon) }}" class="ml-3 inline" onsubmit="return confirm('Supprimer ce code ?')">@csrf @method('DELETE')<button class="text-red-700 hover:underline">Supprimer</button></form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $coupons->links() }}</div>
    @endif
</x-espace.body>
@endsection
