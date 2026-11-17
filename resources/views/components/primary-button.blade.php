<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-gold-400 via-gold-500 to-gold-400 hover:from-gold-300 hover:to-gold-400 text-[#1a1a2e] font-bold text-xs uppercase tracking-wider rounded-xl shadow-md focus:outline-none focus:ring-2 focus:ring-gold-500 focus:ring-offset-2 transition-all duration-200 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
