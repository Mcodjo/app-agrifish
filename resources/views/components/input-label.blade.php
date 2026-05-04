@props(['for' => null, 'value'])

<label @if($for) for="{{ $for }}" @endif {{ $attributes->merge(['class' => 'block text-sm font-semibold text-slate-700']) }}>
    {{ $value ?? $slot }}
</label>
