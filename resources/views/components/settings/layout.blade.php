<div class="flex items-start max-md:flex-col lg:gap-8 gap-4">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist aria-label="{{ __('Settings') }}">
            <flux:navlist.item :href="route('profile.edit')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
            <flux:navlist.item :href="route('user-password.edit')" wire:navigate>{{ __('Password') }}
            </flux:navlist.item>
            <flux:navlist.item :href="route('appearance.edit')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 w-full max-md:pt-6">
        <div class="theme-surface relative overflow-hidden rounded-2xl border border-zinc-200 bg-white/50 p-6 shadow-sm backdrop-blur-md dark:border-zinc-700/50 dark:bg-zinc-900/50 sm:p-8">
            <div class="mb-6">
                <flux:heading size="lg">{{ $heading ?? '' }}</flux:heading>
                <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>
            </div>
            
            <flux:separator variant="subtle" class="mb-6" />

            <div class="w-full max-w-lg">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
