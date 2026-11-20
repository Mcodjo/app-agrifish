@props(['items', 'inverse' => false])

<ol {{ $attributes->merge(['class' => 'grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-4']) }}>
    @foreach($items as $i => [$titre, $texte])
        <li class="border-t pt-5 {{ $inverse ? 'border-white/20' : 'border-dark/15' }}">
            <span class="font-serif text-lg {{ $inverse ? 'text-gold-light' : 'text-gold-dark' }}">{{ sprintf('%02d', $i + 1) }}</span>
            <h3 class="mt-3 text-lg font-semibold {{ $inverse ? 'text-white' : 'text-dark' }}">{{ $titre }}</h3>
            <p class="mt-2 text-[15px] leading-7 {{ $inverse ? 'text-white/70' : 'text-muted' }}">{{ $texte }}</p>
        </li>
    @endforeach
</ol>
