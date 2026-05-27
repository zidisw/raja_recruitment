<flux:dropdown position="top" align="start" class="w-full">
    <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()" icon-trailing="chevron-up-down"
        class="w-full" />

    <flux:menu class="w-64">
        <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-200 dark:border-white/10">
            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />
            <div class="grid flex-1 text-start text-sm leading-tight">
                <div class="font-semibold text-gray-800 dark:text-white truncate">{{ auth()->user()->name }}</div>
                <div class="text-xs text-brand-600 dark:text-brand-400 truncate">{{ auth()->user()->email }}</div>
            </div>
        </div>

        @if (!auth()->user()->role->isUser())
            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate
                class="mt-2 text-zinc-700 dark:text-zinc-300">
                {{ __('Settings') }}
            </flux:menu.item>
        @endif

        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <flux:menu.item as="button" type="submit" onclick="this.closest('form').submit();" icon="arrow-right-start-on-rectangle"
                class="w-full text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10"
                data-test="logout-button">
                {{ __('Log out') }}
            </flux:menu.item>
        </form>
    </flux:menu>
</flux:dropdown>