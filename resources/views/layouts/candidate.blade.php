<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ request()->cookie('theme') === 'dark' ? 'dark' : '' }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 antialiased dark:bg-zinc-950">
        @include('partials.navbar')

        <div
            x-data="{ show: false, message: '', type: 'success', timer: null, open(event) { this.message = event.detail.message ?? event.detail[0]?.message ?? ''; this.type = event.detail.type ?? event.detail[0]?.type ?? 'success'; this.show = true; if (this.timer) clearTimeout(this.timer); this.timer = setTimeout(() => this.show = false, 3500); } }"
            x-init="if (@js(session('success'))) open({ detail: { message: @js(session('success')), type: 'success' } }); if (@js(session('error'))) open({ detail: { message: @js(session('error')), type: 'error' } });"
            @notify.window="open($event)" class="fixed top-4 right-4 z-50 w-full max-w-sm px-4 pointer-events-none">
            <div x-show="show" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                :class="type === 'error'
                    ? 'border-red-200 bg-red-50 text-red-800 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300'
                    : 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300'"
                class="pointer-events-auto rounded-xl border px-4 py-3 shadow-xl backdrop-blur">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium leading-relaxed" x-text="message"></p>
                    <button type="button" @click="show = false"
                        class="shrink-0 rounded-md p-1 text-zinc-400 hover:bg-black/5 hover:text-zinc-600 dark:hover:bg-white/10 dark:hover:text-zinc-200"
                        aria-label="Close notification">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        @livewireScripts
        @fluxScripts
    </body>
</html>
