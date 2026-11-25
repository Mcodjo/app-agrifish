@props(['label', 'value', 'hint' => null, 'tone' => null, 'href' => null])

@php $tag = $href ? 'a' : 'div'; @endphp
<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => 'block rounded-xl border border-line bg-white px-5 py-4'.($href ? ' transition-colors hover:border-dark/25' : '')]) }}>
    <p class="text-sm text-muted">{{ $label }}</p>
    <p class="mt-1 text-2xl font-semibold tabular-nums {{ $tone === 'warning' ? 'text-amber-800' : 'text-dark' }}">{{ $value }}</p>
    @if($hint)
        <p class="mt-0.5 text-xs text-muted">{{ $hint }}</p>
    @endif
</{{ $tag }}>
