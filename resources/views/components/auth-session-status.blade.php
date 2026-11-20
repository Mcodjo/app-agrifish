@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-xl border border-primary-200 bg-primary-50 px-4 py-3 text-sm text-primary-900']) }}>
        {{ $status }}
    </div>
@endif
