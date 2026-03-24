@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-2 custom-dropdown-surface bg-white/90 dark:bg-zinc-900/90 backdrop-blur shadow-xl'])

@php
    $isBottom = str_starts_with($align, 'bottom');

    $widthClasses = match ($width) {
        '48' => 'w-48',
        '64' => 'w-64',
        '80' => 'w-80',
        default => $width,
    };

    $enterStart = 'opacity-0';
    $enterEnd = 'opacity-100';
    $leaveStart = 'opacity-100';
    $leaveEnd = 'opacity-0';
@endphp

<div {{ $attributes->merge(['class' => 'relative']) }} x-data="{
    open: false,
    popupStyle: {},
    updatePosition() {
        const trigger = this.$refs.trigger;
        if (!trigger) return;
        const rect = trigger.getBoundingClientRect();
        
        let panelHeight = 300;
        if (this.$refs.panel && this.$refs.panel.offsetHeight > 0) {
            panelHeight = this.$refs.panel.offsetHeight;
        }

        @if($isBottom)
            this.popupStyle = {
                position: 'fixed',
                bottom: (window.innerHeight - rect.top + 8) + 'px',
                @if($align === 'bottom-end')
                    right: (window.innerWidth - rect.right) + 'px',
                @else
                    left: rect.left + 'px',
                @endif
                zIndex: 9999,
            };
        @else
            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceAbove = rect.top;
            let topPosition = rect.bottom + 8;
            
            if (spaceBelow < panelHeight && spaceAbove > spaceBelow) {
                topPosition = rect.top - panelHeight - 8;
            }

            this.popupStyle = {
                position: 'fixed',
                top: topPosition + 'px',
                @if($align === 'left')
                    left: rect.left + 'px',
                @else
                    right: (window.innerWidth - rect.right) + 'px',
                @endif
                zIndex: 9999,
            };
        @endif
    },
    toggle() {
        this.open = !this.open;
        if (this.open) {
            this.updatePosition();
            this.$nextTick(() => { this.updatePosition(); });
        }
    }
}" @click.outside="open = false" @close.stop="open = false" @scroll.window="if(open) updatePosition()"
    @resize.window="if(open) updatePosition()">
    <div x-ref="trigger" @click="toggle()"
        class="w-full min-w-0 cursor-pointer transition-transform duration-200 active:scale-95">
        {{ $trigger }}
    </div>

    <template x-teleport="body">
        <div x-ref="panel" x-show="open" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="{{ $enterStart }}" x-transition:enter-end="{{ $enterEnd }}"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="{{ $leaveStart }}"
            x-transition:leave-end="{{ $leaveEnd }}" :style="popupStyle" class="custom-dropdown-panel {{ $widthClasses }}"
            style="display: none;" @click="open = false">

            <div
                class="rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] dark:shadow-[0_8px_30px_rgba(255,255,255,0.05)] border border-white/40 dark:border-white/10 overflow-hidden overflow-y-auto max-h-[60vh] {{ $contentClasses }}">
                {{ $content }}
            </div>
        </div>
    </template>
</div>