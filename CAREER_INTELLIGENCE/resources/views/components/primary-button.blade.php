<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#004AAD] hover:bg-[#003B8B] border border-transparent rounded-xl font-bold text-xs text-white shadow-md shadow-[#004AAD]/20 uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-primary focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>





