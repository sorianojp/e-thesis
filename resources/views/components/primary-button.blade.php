
<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#050565] hover:text-[#050565] hover:bg-transparent border hover:border-[#050565] rounded-md font-semibold text-xs text-white uppercase tracking-widest  active:bg-[#050565] active:text-white focus:outline-none focus:ring-2 focus:ring-[#050565] focus:ring-offset-2 transition ease-in-out duration-150 ']) }}>
    {{ $slot }}
</button>

