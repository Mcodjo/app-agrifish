@props([
    'title' => 'Parlons de votre projet',
    'text' => 'Décrivez votre besoin en quelques lignes. Nous revenons vers vous sous 24 heures ouvrées pour convenir d’un premier échange.',
    'primary' => ['Nous contacter', null],
    'secondary' => null,
])

@php
    $primaryUrl = $primary[1] ?? route('contact');
@endphp

<div class="max-w-content px-site">
    <div class="grid gap-8 border-b border-white/10 py-16 sm:py-20 lg:grid-cols-[1.4fr_1fr] lg:items-end">
        <div>
            <h2 class="font-serif text-4xl leading-tight tracking-[-0.015em] text-white sm:text-5xl">{{ $title }}</h2>
            <p class="mt-5 max-w-xl text-lg leading-8 text-white/70">{{ $text }}</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row lg:justify-end">
            <a href="{{ $primaryUrl }}" class="btn-light">{{ $primary[0] }}</a>
            @if($secondary)
                <a href="{{ $secondary[1] }}" class="btn-outline-light">{{ $secondary[0] }}</a>
            @endif
        </div>
    </div>
</div>
