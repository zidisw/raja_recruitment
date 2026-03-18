<nav x-data="{
    mobileMenuOpen: false,
    isDark: false,
    initTheme() {
        let theme = localStorage.getItem('flux.appearance') || localStorage.getItem('theme');
        if (!theme) {
            theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        localStorage.setItem('flux.appearance', theme);
        localStorage.setItem('theme', theme);
        document.cookie = 'theme=' + theme + ';path=/;max-age=31536000';

        this.isDark = theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (this.isDark) {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
        }
    },
    toggleTheme() {
        this.isDark = !this.isDark;
        if (this.isDark) {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
            localStorage.setItem('flux.appearance', 'dark');
            localStorage.setItem('theme', 'dark');
            document.cookie = 'theme=dark;path=/;max-age=31536000';
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
            localStorage.setItem('flux.appearance', 'light');
            localStorage.setItem('theme', 'light');
            document.cookie = 'theme=light;path=/;max-age=31536000';
        }
    }
}" x-init="initTheme()"
    class="sticky top-0 z-50 w-full bg-white dark:bg-slate-900 shadow-sm border-b border-slate-200 dark:border-slate-800 transition-colors duration-300">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <div class="shrink-0">
                <a href="{{ route('home') }}">
                    <img class="h-10 transition-transform duration-300 sm:h-12 hover:scale-105 dark:hidden"
                        src="{{ asset('rjs-photos/LOGO-RJS-tanpa-work-scoop-300x80.png') }}"
                        alt="PT Roda Jaya Sakti Logo">
                    <img class="hidden h-10 transition-transform duration-300 sm:h-12 hover:scale-105 dark:block"
                        src="{{ asset('rajawhite.svg') }}" alt="PT Roda Jaya Sakti Logo White">
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
                        <a href="{{ auth()->user()->role->isUser() ? route('candidate.dashboard') : route('dashboard') }}"
                            class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white transition-all rounded-full bg-brand-500 hover:bg-brand-600 hover:shadow-lg hover:shadow-brand-500/30">
                            Dashboard
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
                        aria-label="Toggle Dark Mode">
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
                    aria-label="Toggle Dark Mode">
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
                    class="p-2 text-slate-800 dark:text-white focus:outline-none transition-colors rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" style="display: none;" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="mobileMenuOpen" x-transition.opacity style="display: none;"
        class="absolute w-full bg-white dark:bg-slate-900 shadow-2xl md:hidden top-full border-t border-slate-100 dark:border-slate-800 z-50">
        <div class="px-4 py-6 space-y-4">
            <a href="{{ route('home') }}" @click="mobileMenuOpen = false"
                class="block text-base font-semibold {{ request()->routeIs('home') ? 'text-brand-500' : 'text-slate-800 dark:text-slate-200 hover:text-brand-500' }}">Home</a>
            <a href="{{ route('about') }}" @click="mobileMenuOpen = false"
                class="block text-base font-semibold {{ request()->routeIs('about') ? 'text-brand-500' : 'text-slate-800 dark:text-slate-200 hover:text-brand-500' }}">About
                Us</a>
            <a href="{{ route('articles.index') }}" @click="mobileMenuOpen = false"
                class="block text-base font-semibold {{ request()->routeIs('articles.*') ? 'text-brand-500' : 'text-slate-800 dark:text-slate-200 hover:text-brand-500' }}">News</a>
            <a href="{{ route('careers.index') }}" @click="mobileMenuOpen = false"
                class="block text-base font-semibold {{ request()->routeIs('careers.*') ? 'text-brand-500' : 'text-slate-800 dark:text-slate-200 hover:text-brand-500' }}">Job
                Portal</a>
            <a href="{{ route('contact') }}" @click="mobileMenuOpen = false"
                class="block text-base font-semibold {{ request()->routeIs('contact') ? 'text-brand-500' : 'text-slate-800 dark:text-slate-200 hover:text-brand-500' }}">Contact</a>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                @auth
                    <a href="{{ auth()->user()->role->isUser() ? route('candidate.dashboard') : route('dashboard') }}"
                        class="flex items-center justify-center w-full px-4 py-3 text-base font-bold text-white rounded-xl bg-brand-500 hover:bg-brand-600">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="flex items-center justify-center w-full px-4 py-3 text-base font-bold text-white rounded-xl bg-slate-900 dark:bg-brand-500 hover:bg-brand-500">
                        Login Portal
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>