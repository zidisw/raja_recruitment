@props([
    'options' => [],
    'placeholder' => 'Select...',
    'searchable' => false,
    'model' => null,
    'live' => false,
])

@php
    $wireModifier = $live ? '.live' : '';
    $wireDirective = $model ? "wire:model{$wireModifier}={$model}" : '';
@endphp

<div
    x-data="{
        open: false,
        dropUp: false,
        search: '',
        selected: @entangle($attributes->wire('model')),
        options: {{ Illuminate\Support\Js::from(collect($options)->map(fn($label, $value) => ['value' => (string)$value, 'label' => $label])->values()) }},
        get filteredOptions() {
            if (!this.search) return this.options;
            return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
        },
        get selectedLabel() {
            const found = this.options.find(o => o.value == this.selected);
            return found ? found.label : '{{ $placeholder }}';
        },
        select(value) {
            this.selected = value;
            this.open = false;
            this.search = '';
        },
        updateDirection() {
            const trigger = this.$refs.trigger;
            if (!trigger) return;

            const rect = trigger.getBoundingClientRect();
            const estimatedPanelHeight = this.searchable ? 300 : 260;
            const spaceBelow = window.innerHeight - rect.bottom;

            this.dropUp = spaceBelow < estimatedPanelHeight;
        }
    }"
    x-init="window.addEventListener('resize', () => { if (open) updateDirection(); })"
    @click.outside="open = false; search = ''"
    class="relative custom-select"
>
    {{-- Trigger Button --}}
    <button
        x-ref="trigger"
        @click="open = !open; if (open) updateDirection()"
        type="button"
        class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-xl border border-zinc-200/80 bg-white/60 px-3 py-2 text-sm shadow-sm backdrop-blur-sm hover:border-zinc-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-white/10 dark:bg-zinc-900/60 dark:hover:border-white/20 dark:focus:ring-brand-400/20"
        :class="open ? 'ring-2 ring-brand-500/30 border-brand-400 dark:ring-brand-400/20 dark:border-brand-500' : ''"
    >
        <span
            :class="selected ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-400 dark:text-zinc-500'"
            class="truncate"
            x-text="selectedLabel"
        ></span>
        <svg class="size-4 shrink-0 text-zinc-400" :class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
        </svg>
    </button>

    {{-- Dropdown Panel --}}
    <div
        x-show="open"
        x-cloak
        x-transition.opacity.duration.120ms
        class="custom-select-panel absolute right-0 z-50 min-w-40 max-w-[calc(100vw-1rem)] overflow-hidden rounded-xl border border-zinc-200/60 bg-white/80 shadow-[0_10px_40px_rgba(0,0,0,0.08)] backdrop-blur dark:border-white/10 dark:bg-zinc-900/80 dark:shadow-[0_10px_40px_rgba(0,0,0,0.3)]"
        :class="dropUp ? 'bottom-full mb-2' : 'top-full mt-2'"
        style="display: none;"
    >
        @if($searchable)
            <div class="custom-select-search-wrap border-b border-zinc-100 p-2 dark:border-white/5">
                <input
                    x-model="search"
                    type="text"
                    placeholder="{{ __('Search...') }}"
                    class="custom-select-search-input w-full rounded-lg border-0 bg-zinc-50/80 px-3 py-1.5 text-sm text-zinc-800 placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-brand-500/40 dark:bg-zinc-800/60 dark:text-zinc-200 dark:placeholder-zinc-500"
                    @click.stop
                    x-ref="searchInput"
                >
            </div>
        @endif

        <div class="max-h-60 overflow-y-auto p-1">
            <template x-for="option in filteredOptions" :key="option.value">
                <button
                    @click="select(option.value)"
                    type="button"
                    class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm transition-colors duration-100"
                    :class="selected == option.value
                        ? 'bg-brand-50 text-brand-700 font-medium dark:bg-brand-500/10 dark:text-brand-400'
                        : 'text-zinc-700 hover:bg-zinc-50 dark:text-zinc-300 dark:hover:bg-zinc-800/60'"
                >
                    <span x-text="option.label" class="truncate"></span>
                    <svg x-show="selected == option.value" class="ml-auto size-4 shrink-0 text-brand-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                </button>
            </template>

            <div x-show="filteredOptions.length === 0" class="px-3 py-4 text-center text-sm text-zinc-400">
                {{ __('No options found') }}
            </div>
        </div>
    </div>
</div>
