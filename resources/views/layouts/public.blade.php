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

    @livewireStyles

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

    @include('partials.navbar')

    @hasSection('content')
        @yield('content')
    @else
        {{ $slot }}
    @endif

    @include('partials.footer')

    @livewireScripts
    @fluxScripts
    @stack('scripts')
</body>

</html>