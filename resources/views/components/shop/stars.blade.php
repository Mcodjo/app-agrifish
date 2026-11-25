@props(['value' => 0])

<span class="inline-flex items-center gap-0.5" role="img" aria-label="{{ number_format((float) $value, 1, ',', '') }} sur 5">
    @for($i = 1; $i <= 5; $i++)
        <svg class="h-4 w-4 {{ $value >= $i - 0.25 ? 'text-gold' : 'text-line' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.9l-5.2 2.7 1-5.8L1.5 7.7l5.9-.9L10 1.5z"/></svg>
    @endfor
</span>
