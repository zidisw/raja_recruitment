<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PT. Roda Jaya Sakti') - Mining, Heavy Equipment & Construction</title>
    <meta name="description"
        content="@yield('meta_description', 'PT. Roda Jaya Sakti - Leading Mining Services Contractor in Eastern Indonesia. Nickel hauling, construction, and heavy equipment support services.')">
    <link rel="icon" href="{{ asset('raja.svg') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('raja.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Prevent FOUC on dark mode -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
            '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            brand: {
                                50: '#FFF9ED',
                                100: '#FEF0D3',
                                400: '#FBCB65',
                                500: '#F5A623',
                                600: '#D98C1A',
                                900: '#7A4E0B',
                            }
                        },
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
    @endif

    @stack('styles')
</head>

<body
    class="font-sans antialiased text-slate-600 bg-slate-50 dark:bg-slate-950 dark:text-slate-400 selection:bg-brand-500 selection:text-white transition-colors duration-300">

    <nav x-data="{
        mobileMenuOpen: false,
        isDark: document.documentElement.classList.contains('dark'),
        toggleTheme() {
            this.isDark = !this.isDark;
            if (this.isDark) {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            }
        }
    }" @keydown.escape.window="mobileMenuOpen = false"
        class="relative z-50 w-full bg-white dark:bg-slate-900 shadow-sm border-b border-slate-200 dark:border-slate-800 transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            @php
                $publicCtaRoute = null;
                $publicCtaLabel = null;

                if (auth()->check()) {
                    $authUser = auth()->user();

                    if ($authUser->hasUserRole()) {
                        $publicCtaRoute = $authUser->profile ? route('candidate.dashboard') : route('candidate.profile.setup');
                        $publicCtaLabel = $authUser->profile ? 'Portal Kandidat' : 'Lengkapi Profil';
                    } else {
                        $publicCtaRoute = route('dashboard');
                        $publicCtaLabel = 'Recruitment Dashboard';
                    }
                }
            @endphp
            <div class="flex items-center justify-between h-20">
                <div class="shrink-0">
                    <a href="{{ route('home') }}">
                        <img class="h-10 transition-transform duration-300 sm:h-12 hover:scale-105"
                            src="{{ asset('rjs-photos/LOGO-RJS-tanpa-work-scoop-300x80.png') }}"
                            alt="PT Roda Jaya Sakti Logo">
                    </a>
                </div>

                <div class="hidden md:flex md:items-center md:space-x-8">
                    <a href="{{ route('home') }}"
                        class="text-sm font-semibold {{ request()->routeIs('home') ? 'text-brand-500' : 'text-slate-700 dark:text-slate-200 hover:text-brand-500' }} transition-colors">Home</a>
                    <a href="{{ route('about') }}"
                        class="text-sm font-semibold {{ request()->routeIs('about') ? 'text-brand-500' : 'text-slate-700 dark:text-slate-200 hover:text-brand-500' }} transition-colors">About
                        Us</a>
                    <a href="{{ route('articles.index') }}"
                        class="text-sm font-semibold {{ request()->routeIs('articles.*') ? 'text-brand-500' : 'text-slate-700 dark:text-slate-200 hover:text-brand-500' }} transition-colors">News</a>
                    <a href="{{ route('careers.index') }}"
                        class="text-sm font-semibold {{ request()->routeIs('careers.*') ? 'text-brand-500' : 'text-slate-700 dark:text-slate-200 hover:text-brand-500' }} transition-colors">Job
                        Portal</a>
                    <a href="{{ route('contact') }}"
                        class="text-sm font-semibold {{ request()->routeIs('contact') ? 'text-brand-500' : 'text-slate-700 dark:text-slate-200 hover:text-brand-500' }} transition-colors">Contact</a>

                    <div class="pl-6 border-l border-slate-200 dark:border-slate-700 flex items-center space-x-4">
                        @auth
                            <a href="{{ $publicCtaRoute }}"
                                class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white transition-all rounded-full bg-brand-500 hover:bg-brand-600 hover:shadow-lg hover:shadow-brand-500/30">
                                {{ $publicCtaLabel }}
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white bg-slate-900 dark:bg-brand-500 hover:bg-brand-500 dark:hover:bg-brand-400 transition-all rounded-full hover:shadow-lg hover:shadow-brand-500/30">
                                Login Portal
                            </a>
                        @endauth

                        <!-- Desktop Theme Toggle -->
                        <button @click="toggleTheme()" type="button"
                            class="p-2 text-slate-500 dark:text-slate-400 hover:text-brand-500 dark:hover:text-brand-400 focus:outline-none transition-colors rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                            :aria-pressed="isDark ? 'true' : 'false'" aria-label="Toggle Dark Mode">
                            <svg x-show="isDark" style="display: none;" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg x-show="!isDark" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-3 md:hidden">
                    <!-- Mobile Theme Toggle -->
                    <button @click="toggleTheme()" type="button"
                        class="p-2 text-slate-500 dark:text-slate-400 hover:text-brand-500 focus:outline-none transition-colors rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                        :aria-pressed="isDark ? 'true' : 'false'" aria-label="Toggle Dark Mode">
                        <svg x-show="isDark" style="display: none;" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="!isDark" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                        :aria-expanded="mobileMenuOpen ? 'true' : 'false'" aria-controls="public-mobile-menu"
                        class="p-2 text-slate-800 dark:text-white focus:outline-none transition-colors rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenuOpen" style="display: none;" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="public-mobile-menu" x-show="mobileMenuOpen" x-cloak x-transition.opacity style="display: none;"
            class="absolute w-full bg-white dark:bg-slate-900 shadow-2xl md:hidden top-full border-t border-slate-100 dark:border-slate-800 z-50">
            <div class="px-4 py-6 space-y-4">
                <a href="{{ route('home') }}" @click="mobileMenuOpen = false"
                    class="block text-base font-semibold text-slate-800 dark:text-slate-200 hover:text-brand-500">Home</a>
                <a href="{{ route('about') }}" @click="mobileMenuOpen = false"
                    class="block text-base font-semibold text-slate-800 dark:text-slate-200 hover:text-brand-500">About
                    Us</a>
                <a href="{{ route('articles.index') }}" @click="mobileMenuOpen = false"
                    class="block text-base font-semibold text-slate-800 dark:text-slate-200 hover:text-brand-500">News</a>
                <a href="{{ route('careers.index') }}" @click="mobileMenuOpen = false"
                    class="block text-base font-semibold text-slate-800 dark:text-slate-200 hover:text-brand-500">Job
                    Portal</a>
                <a href="{{ route('contact') }}" @click="mobileMenuOpen = false"
                    class="block text-base font-semibold text-slate-800 dark:text-slate-200 hover:text-brand-500">Contact</a>
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                    @auth
                        <a href="{{ $publicCtaRoute }}"
                            class="flex items-center justify-center w-full px-4 py-3 text-base font-bold text-white rounded-xl bg-brand-500 hover:bg-brand-600">
                            {{ $publicCtaLabel }}
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="flex items-center justify-center w-full px-4 py-3 text-base font-bold text-white rounded-xl bg-slate-900 dark:bg-brand-500 hover:bg-brand-500">Login
                            Portal</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @hasSection('content')
        @yield('content')
    @else
        {{ $slot }}
    @endif

    <footer
        class="bg-white dark:bg-slate-950 pt-20 pb-10 border-t border-slate-100 dark:border-slate-800 transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <img class="h-10 mb-6 dark:brightness-0 dark:invert transition-all"
                        src="{{ asset('rjs-photos/LOGO-RJS-tanpa-work-scoop-300x80.png') }}" alt="RJS Logo">
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6 max-w-md">
                        PT Roda Jaya Sakti (RJS) is committed to being the premier Mining Services Contractor,
                        providing
                        sustainable added value to our business partners' operations.
                    </p>
                </div>

                <div>
                    <h4 class="text-sm font-bold tracking-wider text-slate-900 dark:text-white uppercase mb-6">
                        Navigation</h4>
                    <ul class="space-y-4 text-sm text-slate-500 dark:text-slate-400">
                        <li><a href="{{ route('home') }}" class="hover:text-brand-500 transition-colors">Home</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-brand-500 transition-colors">About Us</a>
                        </li>
                        <li><a href="{{ route('articles.index') }}"
                                class="hover:text-brand-500 transition-colors">Company News</a></li>
                        <li><a href="{{ route('careers.index') }}" class="hover:text-brand-500 transition-colors">Career
                                Portal</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-brand-500 transition-colors">Contact</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-bold tracking-wider text-slate-900 dark:text-white uppercase mb-6">Contact
                        Us</h4>
                    <ul class="space-y-4 text-sm text-slate-500 dark:text-slate-400">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-3 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Jl. Tupai No 71 A, Makassar, South Sulawesi</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>(0411) 850663</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>info@rodajayasakti.id</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div
                class="pt-8 mt-12 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4 transition-colors">
                <p class="text-sm text-slate-400">
                    &copy; {{ date('Y') }} PT. Roda Jaya Sakti. All rights reserved.
                </p>
                <div class="text-sm text-slate-400">
                    ATS & Career Portal
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>