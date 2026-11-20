@props(['for' => null, 'value'])

<label @if($for) for="{{ $for }}" @endif {{ $attributes->merge(['class' => 'field-label']) }}>
    {{ $value ?? $slot }}
</label>
