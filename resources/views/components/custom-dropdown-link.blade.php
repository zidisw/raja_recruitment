<a {{ $attributes->merge(['class' => 'block w-[calc(100%-16px)] px-4 py-2 mx-2 my-1 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-900/30 dark:hover:text-brand-400 rounded-xl transition-all duration-200 ease-in-out cursor-pointer focus:outline-none focus:ring-2 focus:ring-brand-500']) }}>
    {{ $slot }}
</a>
