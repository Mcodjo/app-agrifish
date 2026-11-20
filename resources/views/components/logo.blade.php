@props(['inverse' => false])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <x-application-logo class="h-8 w-8 {{ $inverse ? 'text-white' : 'text-primary-800' }}" />
    <span class="text-[1.35rem] font-bold tracking-[-0.02em] {{ $inverse ? 'text-white' : 'text-dark' }}">Agri<span class="{{ $inverse ? 'text-gold-light' : 'text-gold' }}">Fish</span></span>
</span>
