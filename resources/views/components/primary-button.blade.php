<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-xl bg-gold px-5 py-3 text-sm font-bold text-white shadow-md transition-all hover:bg-gold-dark hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-gold/20 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
