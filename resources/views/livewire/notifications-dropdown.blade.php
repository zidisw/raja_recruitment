<div>
    <flux:dropdown position="bottom" align="end">
        <button type="button"
            class="relative rounded-full p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer border-none outline-none">
            <flux:icon.bell class="size-6 text-zinc-600 dark:text-zinc-400" />
            @if($this->unreadCount > 0)
                <span class="absolute top-1 right-1 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span
                        class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white dark:border-zinc-900"></span>
                </span>
            @endif
        </button>

        <flux:menu class="w-80">
            <div class="flex items-center justify-between mb-2 px-4 py-2 border-b border-zinc-100 dark:border-zinc-800">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Notifications') }}</h3>
                @if($this->unreadCount > 0)
                    <button wire:click="markAllAsRead" type="button"
                        class="text-xs text-brand-500 hover:text-brand-600 font-medium cursor-pointer">
                        {{ __('Mark all as read') }}
                    </button>
                @endif
            </div>

            <div class="space-y-1 max-h-96 overflow-y-auto w-full px-2">
                @forelse($this->notifications as $notification)
                    <flux:menu.item class="flex gap-3 items-start p-3! cursor-pointer group w-full"
                        wire:click="markAsRead('{{ $notification->id }}')">
                        <div class="shrink-0 mt-1">
                            <div
                                class="size-8 rounded-full bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center text-brand-500">
                                @if(isset($notification->data['type']) && $notification->data['type'] === 'status_update')
                                    <flux:icon.arrow-path class="size-4" />
                                @else
                                    <flux:icon.document-text class="size-4" />
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 min-w-0 w-full overflow-hidden">
                            <p class="text-xs font-medium text-zinc-900 dark:text-zinc-100 truncate">
                                {{ $notification->data['job_title'] ?? 'Notification' }}
                            </p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2 wrap-break-word">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <p class="text-[10px] text-zinc-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </flux:menu.item>
                @empty
                    <div class="py-6 text-center text-sm text-zinc-500 w-full">
                        {{ __('No new notifications') }}
                    </div>
                @endforelse
            </div>
        </flux:menu>
    </flux:dropdown>
</div>