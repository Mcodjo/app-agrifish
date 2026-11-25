@props(['tabs', 'current' => null, 'param' => 'filtre', 'keep' => []])

{{-- Onglets de filtre : [valeur => [libellé, nombre]] --}}
<nav class="-mx-1 flex gap-1.5 overflow-x-auto px-1 pb-1" aria-label="Filtres">
    @foreach($tabs as $value => $tab)
        @php
            [$label, $count] = is_array($tab) ? array_pad($tab, 2, null) : [$tab, null];
            $active = (string) $current === (string) $value;
        @endphp
        <a href="{{ request()->url().'?'.http_build_query(array_filter(array_merge($keep, [$param => $value]), fn ($v) => $v !== null && $v !== '')) }}"
           @if($active) aria-current="true" @endif
           class="inline-flex shrink-0 items-center gap-2 rounded-full px-3.5 py-1.5 text-sm font-medium transition {{ $active ? 'bg-dark text-white' : 'bg-white text-dark ring-1 ring-inset ring-line hover:bg-cream' }}">
            {{ $label }}
            @if($count !== null)
                <span class="rounded-full px-1.5 text-xs {{ $active ? 'bg-white/20' : 'bg-cream text-muted' }}">{{ $count }}</span>
            @endif
        </a>
    @endforeach
</nav>
