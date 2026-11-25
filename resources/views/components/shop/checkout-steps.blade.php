@props(['current' => 1])

@php $steps = [1 => 'Panier', 2 => 'Coordonnées et paiement', 3 => 'Confirmation']; @endphp
<ol {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-x-3 gap-y-2 text-sm']) }} aria-label="Étapes de la commande">
    @foreach($steps as $number => $label)
        <li class="flex items-center gap-2 {{ $number === $current ? 'font-semibold text-dark' : ($number < $current ? 'text-primary-800' : 'text-muted') }}" @if($number === $current) aria-current="step" @endif>
            <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs {{ $number === $current ? 'bg-dark text-white' : ($number < $current ? 'bg-primary-100 text-primary-800' : 'bg-white text-muted ring-1 ring-line') }}">{{ $number }}</span>
            {{ $label }}
        </li>
        @unless($loop->last)<li class="text-line" aria-hidden="true">—</li>@endunless
    @endforeach
</ol>
