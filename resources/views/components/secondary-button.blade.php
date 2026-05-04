<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-xl border border-primary px-4 py-2.5 text-sm font-semibold text-primary shadow-sm transition-colors hover:bg-primary hover:text-white focus:outline-none focus:ring-4 focus:ring-primary/15 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
