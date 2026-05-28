<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="{{ request()->cookie('theme') === 'dark' ? 'dark' : '' }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen futuristic-bg">
    <flux:sidebar sticky collapsible="mobile" class="sidebar-modern relative">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ url('/') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <div class="flex-1 min-h-0 relative flex flex-col w-full" 
             x-data="{ 
                 showTopShadow: false, 
                 showBottomShadow: false,
                 checkScroll() {
                     const el = this.$refs.scrollableNav;
                     if (!el) return;
                     this.showTopShadow = el.scrollTop > 5;
                     this.showBottomShadow = Math.ceil(el.scrollTop + el.clientHeight) < el.scrollHeight - 5;
                 }
             }" 
             x-init="
                setTimeout(() => checkScroll(), 100);
                const observer = new MutationObserver(() => checkScroll());
                observer.observe($el, { childList: true, subtree: true, attributes: true });
                window.addEventListener('resize', () => checkScroll());
             ">
             
            <!-- Top Shadow Indicator -->
            <div x-show="showTopShadow" 
                 x-transition.opacity.duration.300ms
                 class="absolute top-0 inset-x-0 h-6 bg-linear-to-b from-zinc-50 dark:from-zinc-900 to-transparent z-10 pointer-events-none"></div>

            <div class="flex-1 overflow-y-auto scroll-smooth [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]" 
                 x-ref="scrollableNav" 
                 @scroll.passive="checkScroll()">
                <flux:sidebar.nav class="pb-4">
            @php $role = auth()->user()->role; @endphp

            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home"
                    :href="$role->isUser() ? route('candidate.dashboard') : route('dashboard')"
                    :current="$role->isUser() ? request()->routeIs('candidate.dashboard') : request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            @if ($role->isUser())
                <flux:sidebar.group :heading="__('Portal Kandidat')" class="grid mt-4">
                    <flux:sidebar.item icon="briefcase" :href="route('candidate.portal')"
                        :current="request()->routeIs('candidate.portal')" wire:navigate>
                        {{ __('Lowongan') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="chart-bar" :href="route('candidate.applications')"
                        :current="request()->routeIs('candidate.applications')" wire:navigate>
                        {{ __('Tracking Lamaran') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="user" :href="route('candidate.profile')"
                        :current="request()->routeIs('candidate.profile')" wire:navigate>
                        {{ __('Profil Saya') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            @endif

            @if ($role->canAccessRecruitment())
                <flux:sidebar.group :heading="__('Recruitment')" class="grid mt-4">
                    @if ($role->isAdmin() || $role->isSuperAdmin())
                        <flux:sidebar.item icon="clipboard-document-list" :href="route('ptk.index')"
                            :current="request()->routeIs('ptk.index')" wire:navigate>
                            {{ __('PTK') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="briefcase" :href="route('jobs.index')"
                            :current="request()->routeIs('jobs.index')" wire:navigate>
                            {{ __('Job Management') }}
                        </flux:sidebar.item>
                    @endif

                    <flux:sidebar.item icon="clipboard-document-list" :href="route('candidates.administrasi')"
                        :current="request()->routeIs('candidates.administrasi')" wire:navigate>
                        {{ __('Administrasi') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="arrow-path" :href="route('candidates.on-progress')"
                        :current="request()->routeIs('candidates.on-progress')" wire:navigate>
                        {{ __('On Progress') }}
                    </flux:sidebar.item>

                    @php
                        $isInterviewRoute = request()->routeIs('interviews.*');
                        $totalInterviews = ($hrCount ?? 0) + ($userCount ?? 0);
                    @endphp
                    <div x-data="{ open: @js($isInterviewRoute) }"
                        wire:key="interview-nav-{{ $isInterviewRoute ? '1' : '0' }}" class="space-y-0.5">
                        <button type="button" @click="open = !open" @class([
                            'sidebar-disclosure-button border border-transparent w-full px-3 h-8 flex items-center gap-3 rounded-lg hover:bg-zinc-800/5 dark:hover:bg-white/7 text-zinc-500 hover:text-zinc-800 dark:text-white/80 dark:hover:text-white transition-colors',
                            'bg-zinc-800/5 text-zinc-800 dark:bg-white/10 dark:text-white' => $isInterviewRoute,
                        ])>
                            <flux:icon.calendar class="size-4" />
                            <span class="flex-1 text-left rtl:text-right text-sm font-medium leading-none">
                                {{ __('Interview') }}
                            </span>
                            <svg class="size-4 text-zinc-400 transition-transform duration-200"
                                x-bind:class="open ? 'rotate-90' : ''" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M8.22 5.22a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 010-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1" class="relative ps-7 space-y-0.5">
                            <div
                                class="sidebar-disclosure-rail absolute inset-y-0.75 w-px bg-zinc-200 dark:bg-white/30 inset-s-0 ms-5">
                            </div>

                            <flux:sidebar.item icon="user-group" :href="route('interviews.hr')"
                                :current="request()->routeIs('interviews.hr')" wire:navigate>
                                <div class="flex items-center justify-between w-full">
                                    <span>{{ __('Interview HR') }}</span>
                                </div>
                            </flux:sidebar.item>

                            <flux:sidebar.item icon="user" :href="route('interviews.user')"
                                :current="request()->routeIs('interviews.user')" wire:navigate>
                                <div class="flex items-center justify-between w-full">
                                    <span>{{ __('Interview User') }}</span>
                                </div>
                            </flux:sidebar.item>
                        </div>
                    </div>

                    @if ($role->isAdmin() || $role->isSuperAdmin())
                        <flux:sidebar.item icon="document-check" :href="route('offering.index')"
                            :current="request()->routeIs('offering.index')" wire:navigate>
                            {{ __('Offering Letter') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="beaker" :href="route('psychotest.index')"
                            :current="request()->routeIs('psychotest.index')" wire:navigate>
                            {{ __('Psychotest') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="heart" :href="route('mcu.index')" :current="request()->routeIs('mcu.index')"
                            wire:navigate>
                            {{ __('MCU') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="rocket-launch" :href="route('onboarding.index')"
                            :current="request()->routeIs('onboarding.index')" wire:navigate>
                            {{ __('Onboarding') }}
                        </flux:sidebar.item>
                    @endif

                    <flux:sidebar.item icon="clock" :href="route('candidates.riwayat')"
                        :current="request()->routeIs('candidates.riwayat')" wire:navigate>
                        {{ __('Riwayat Kandidat') }}
                    </flux:sidebar.item>

                    @if ($role->isAdmin() || $role->isSuperAdmin())
                        <flux:sidebar.item icon="envelope-open" :href="route('email-templates.index')"
                            :current="request()->routeIs('email-templates.index')" wire:navigate>
                            Template Email
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>
            @endif

            @if ($role->isSuperAdmin())
                <flux:sidebar.group :heading="__('Administration')" class="grid mt-4">
                    <flux:sidebar.item icon="users" :href="route('users.index')"
                        :current="request()->routeIs('users.index')" wire:navigate>
                        {{ __('User Management') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="building-office-2" :href="route('departments.index')"
                        :current="request()->routeIs('departments.index')" wire:navigate>
                        {{ __('Departments') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="map-pin" :href="route('sites.index')"
                        :current="request()->routeIs('sites.index')" wire:navigate>
                        {{ __('Sites') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="newspaper" :href="route('news.index')"
                        :current="request()->routeIs('news.index')" wire:navigate>
                        {{ __('News Management') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="envelope" :href="route('superadmin.smtp')"
                        :current="request()->routeIs('superadmin.smtp')" wire:navigate>
                        {{ __('Email Settings') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            @endif
                </flux:sidebar.nav>
            </div>
            
            <!-- Bottom Shadow Indicator -->
            <div x-show="showBottomShadow"
                 x-transition.opacity.duration.300ms 
                 class="absolute bottom-0 inset-x-0 h-6 bg-linear-to-t from-zinc-50 dark:from-zinc-900 to-transparent z-10 pointer-events-none"></div>
        </div>

        <div class="hidden lg:flex flex-col gap-3 sidebar-footer overflow-visible max-w-full mt-auto pt-4 border-t border-zinc-200/50 dark:border-white/10 shrink-0">
            {{-- Theme Toggle --}}
            <button x-data x-on:click="
                    const nextAppearance = $flux.dark ? 'light' : 'dark';
                    if (window.Flux?.applyAppearance) {
                        window.Flux.appearance = nextAppearance;
                        window.Flux.applyAppearance(nextAppearance);
                    } else {
                        $flux.appearance = nextAppearance;
                        localStorage.setItem('flux.appearance', nextAppearance);
                    }
                    
                    document.cookie = 'theme=' + nextAppearance + ';path=/;max-age=31536000';

                    if (typeof window.__applyPreferredTheme === 'function') {
                        window.__applyPreferredTheme(nextAppearance);
                    }
                "
                class="sidebar-theme-toggle flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-amber-50 hover:text-amber-700 dark:text-zinc-300 dark:hover:bg-amber-500/10 dark:hover:text-amber-400 truncate w-full">
                {{-- Sun icon (shown in dark mode → switch to light) --}}
                <svg x-show="$flux.dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                        clip-rule="evenodd" />
                </svg>
                {{-- Moon icon (shown in light mode → switch to dark) --}}
                <svg x-show="!$flux.dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                </svg>
                <span class="truncate" x-text="$flux.dark ? @js(__('Light Mode')) : @js(__('Dark Mode'))"></span>
            </button>

            @livewire('notifications-dropdown')
            <x-desktop-user-menu :name="auth()->user()->name" />
        </div>
    </flux:sidebar>

    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <div class="flex items-center gap-3">
            @livewire('notifications-dropdown')

            <flux:dropdown position="top" align="end">
                <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    @if (!auth()->user()->role->isUser())
                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                                {{ __('Settings') }}
                            </flux:menu.item>
                        </flux:menu.radio.group>

                        <flux:menu.separator />
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" onclick="this.closest('form').submit();" icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer" data-test="logout-button">
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @livewireScripts
    @fluxScripts
</body>

</html>
