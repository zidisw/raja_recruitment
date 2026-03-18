@props([])

{{-- Modern Confirmation Dialog — Alpine.js driven --}}
<div
    x-data="{
        open: false,
        title: '',
        description: '',
        variant: 'danger',
        method: '',
        args: [],
        loading: false,
        confirmLabel: '',
        cancelLabel: '{{ __("Batal") }}',

        variants: {
            danger:  { icon: 'trash',     color: 'red',     ring: 'ring-red-500/20',    bg: 'bg-red-50 dark:bg-red-500/10',    iconBg: 'bg-red-100 dark:bg-red-500/20',   iconColor: 'text-red-600 dark:text-red-400',   btnClass: 'bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 focus:ring-red-500/30' },
            warning: { icon: 'exclamation-triangle', color: 'amber', ring: 'ring-amber-500/20', bg: 'bg-amber-50 dark:bg-amber-500/10', iconBg: 'bg-amber-100 dark:bg-amber-500/20', iconColor: 'text-amber-600 dark:text-amber-400', btnClass: 'bg-amber-600 hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600 focus:ring-amber-500/30' },
            info:    { icon: 'arrow-right-circle', color: 'emerald', ring: 'ring-emerald-500/20', bg: 'bg-emerald-50 dark:bg-emerald-500/10', iconBg: 'bg-emerald-100 dark:bg-emerald-500/20', iconColor: 'text-emerald-600 dark:text-emerald-400', btnClass: 'bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 focus:ring-emerald-500/30' },
        },

        get v() { return this.variants[this.variant] || this.variants.danger; },

        show(detail) {
            this.title       = detail.title       || '{{ __("Konfirmasi") }}';
            this.description = detail.description || '';
            this.variant     = detail.variant     || 'danger';
            this.method      = detail.method      || '';
            this.args        = detail.args        || [];
            this.confirmLabel = detail.confirmLabel || (this.variant === 'danger' ? '{{ __("Hapus") }}' : '{{ __("Lanjutkan") }}');
            this.cancelLabel  = detail.cancelLabel || '{{ __("Batal") }}';
            this.loading     = false;
            this.open        = true;
        },

        close() {
            this.open = false;
        },

        async confirm() {
            if (!this.method) return;
            this.loading = true;
            try {
                await $wire.call(this.method, ...this.args);
            } catch (e) {
                console.error('Confirm action error:', e);
            } finally {
                this.loading = false;
                this.open = false;
            }
        }
    }"
    @confirm-action.window="show($event.detail)"
    @keydown.escape.window="open && close()"
    x-cloak
    class="relative z-999"
>
    {{-- Backdrop --}}
    <template x-teleport="body">
        <div x-show="open" class="fixed inset-0 z-9998" aria-hidden="true">
            {{-- Overlay --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/40 backdrop-blur-sm"
                @click="close()"
            ></div>

            {{-- Dialog --}}
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                    @click.stop
                    class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl shadow-black/10 dark:bg-zinc-900 dark:shadow-black/30 border border-zinc-200/60 dark:border-zinc-700/60"
                >
                    {{-- Colored top accent --}}
                    <div class="h-1 w-full" :class="{
                        'bg-linear-to-r from-red-500 to-rose-500': variant === 'danger',
                        'bg-linear-to-r from-amber-500 to-orange-500': variant === 'warning',
                        'bg-linear-to-r from-emerald-500 to-teal-500': variant === 'info'
                    }"></div>

                    <div class="px-6 pt-6 pb-5">
                        {{-- Icon with pulse ring --}}
                        <div class="flex justify-center mb-5">
                            <div class="relative">
                                {{-- Pulse rings --}}
                                <div class="absolute inset-0 rounded-full confirm-pulse-ring" :class="v.ring" style="animation: confirmPulse 2s ease-in-out infinite;"></div>
                                <div class="absolute inset-0 rounded-full confirm-pulse-ring" :class="v.ring" style="animation: confirmPulse 2s ease-in-out 0.5s infinite;"></div>

                                {{-- Icon circle --}}
                                <div class="relative flex h-16 w-16 items-center justify-center rounded-full" :class="v.iconBg">
                                    {{-- Danger icon --}}
                                    <template x-if="variant === 'danger'">
                                        <svg class="h-7 w-7" :class="v.iconColor" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </template>
                                    {{-- Warning icon --}}
                                    <template x-if="variant === 'warning'">
                                        <svg class="h-7 w-7" :class="v.iconColor" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                        </svg>
                                    </template>
                                    {{-- Info / advance icon --}}
                                    <template x-if="variant === 'info'">
                                        <svg class="h-7 w-7" :class="v.iconColor" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Text --}}
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white" x-text="title"></h3>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed" x-text="description" x-show="description"></p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 bg-zinc-50/80 dark:bg-zinc-800/50 px-6 py-4 border-t border-zinc-100 dark:border-zinc-800">
                        <button
                            @click="close()"
                            :disabled="loading"
                            type="button"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-zinc-500/20 disabled:opacity-50"
                        >
                            <span x-text="cancelLabel"></span>
                        </button>
                        <button
                            @click="confirm()"
                            :disabled="loading"
                            type="button"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition-all duration-200 focus:outline-none focus:ring-2 disabled:opacity-60"
                            :class="v.btnClass"
                        >
                            {{-- Loading spinner --}}
                            <svg x-show="loading" class="animate-spin h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="loading ? '{{ __("Memproses...") }}' : confirmLabel"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
