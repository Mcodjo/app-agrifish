@props(['status', 'label' => null, 'size' => 'sm'])

@php
    $styles = [
        // Demandes
        'new' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'reviewing' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'assigned' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'in_progress' => 'bg-primary-50 text-primary-700 ring-primary-200',
        'waiting_client' => 'bg-orange-50 text-orange-700 ring-orange-200',
        'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'closed' => 'bg-gray-100 text-gray-600 ring-gray-200',
        'cancelled' => 'bg-white text-gray-500 ring-gray-200',
        // Étapes
        'planned' => 'bg-gray-100 text-gray-700 ring-gray-200',
        'validated' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'blocked' => 'bg-red-50 text-red-700 ring-red-200',
    ];
    $dots = [
        'new' => 'bg-sky-500', 'reviewing' => 'bg-amber-500', 'assigned' => 'bg-indigo-500',
        'in_progress' => 'bg-primary', 'waiting_client' => 'bg-orange-500', 'completed' => 'bg-emerald-500',
        'closed' => 'bg-gray-400', 'cancelled' => 'bg-gray-300', 'planned' => 'bg-gray-400', 'validated' => 'bg-emerald-500', 'blocked' => 'bg-red-500',
    ];
    $label ??= \App\Models\ServiceRequest::STATUSES[$status] ?? ucfirst(str_replace('_', ' ', $status));
    $sizeClass = $size === 'lg' ? 'px-3 py-1.5 text-sm' : 'px-2.5 py-1 text-xs';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 whitespace-nowrap rounded-full font-medium ring-1 ring-inset {$sizeClass} ".($styles[$status] ?? 'bg-gray-100 text-gray-700 ring-gray-200')]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $dots[$status] ?? 'bg-gray-400' }}" aria-hidden="true"></span>
    {{ $label }}
</span>
