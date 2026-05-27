<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PT. Roda Jaya Sakti - Mining, Heavy Equipment & Construction</title>
    <link rel="icon" href="{{ asset('raja.svg') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('raja.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Prevent FOUC: resolve appearance from both keys and apply immediately -->
    <script>
        (function() {
            var fa = localStorage.getItem('flux.appearance');
            var theme = fa || localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                document.documentElement.style.colorScheme = 'dark';
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.style.colorScheme = 'light';
            }
        })();
    </script>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-slate-600 bg-slate-50 dark:bg-slate-950 dark:text-slate-400 selection:bg-brand-500 selection:text-white">

    @include('partials.navbar')

    <div x-data="{ slide: 0, init() { setInterval(() => this.slide = (this.slide + 1) % 5, 6000) } }"
        class="relative bg-slate-900 py-20 lg:py-32 overflow-hidden min-h-[80vh] flex items-center">
        <div class="absolute inset-0 z-0 overflow-hidden">
            @foreach ([
                'rjs-photos/webp/new-tampak-atas/8.webp',
                'rjs-photos/webp/new-tampak-atas/9.webp',
                'rjs-photos/webp/new-tampak-atas/11.webp',
                'rjs-photos/webp/new-tampak-atas/image4.webp',
                'rjs-photos/webp/new-tampak-atas/image5.webp',
            ] as $idx => $img)
                <img src="{{ asset($img) }}" alt="Mining Operations" style="display:none;"
                    x-show="slide === {{ $idx }}"
                    x-transition:enter="transition-opacity duration-[1500ms]"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-60"
                    x-transition:leave="transition-opacity duration-[1500ms]"
                    x-transition:leave-start="opacity-60"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0 object-cover w-full h-full">
            @endforeach
            <div class="absolute inset-0 bg-linear-to-r from-slate-950 via-slate-900/90 to-slate-900/40"></div>
        </div>

        <div class="relative z-10 w-full px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <div
                    class="inline-flex items-center px-4 py-2 mb-6 border rounded-full bg-brand-500/10 border-brand-500/30 backdrop-blur-sm">
                    <span class="w-2 h-2 mr-2 rounded-full bg-brand-500 animate-pulse"></span>
                    <span class="text-xs font-bold tracking-widest text-brand-400 uppercase">Excellence In Every
                        Operation</span>
                </div>

                <h1
                    class="mb-6 text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-7xl leading-[1.1]">
                    From the field to the <span
                        class="text-transparent bg-clip-text bg-linear-to-r from-brand-400 to-brand-600">final
                        result.</span>
                </h1>

                <p class="max-w-2xl mb-10 text-lg leading-relaxed text-slate-300 sm:text-xl">
                    Dedicated to delivering uncompromising efficiency, safety, and quality across the mining sector,
                    heavy equipment rental, and large-scale construction projects.
                </p>

                <div class="flex flex-col gap-4 sm:flex-row">
                    <a href="#services"
                        class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all rounded-xl bg-brand-500 hover:bg-brand-400 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-500/30">
                        Explore Our Services
                    </a>
                    <a href="{{ route('careers.index') }}"
                        class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all bg-white/10 border border-white/20 backdrop-blur-md rounded-xl hover:bg-white/20 hover:border-white/40 hover:-translate-y-1">
                        Career Portal
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="relative z-20 px-4 mx-auto -mt-16 lg:-mt-24 max-w-7xl sm:px-6 lg:px-8">
        <div
            class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-2xl rounded-3xl p-8 sm:p-12 shadow-slate-200/50 dark:shadow-none transition-colors duration-300">
            <div
                class="grid grid-cols-1 gap-8 text-center divide-y md:grid-cols-3 md:divide-y-0 md:divide-x divide-slate-100 dark:divide-slate-800">
                <div class="flex flex-col items-center justify-center px-4">
                    <dt
                        class="order-2 mt-3 text-sm font-bold tracking-widest uppercase text-slate-500 dark:text-slate-400">
                        Heavy Equipment Units</dt>
                    <dd class="order-1 text-5xl font-extrabold text-slate-900 dark:text-white">641<span
                            class="text-brand-500">+</span></dd>
                </div>
                <div class="flex flex-col items-center justify-center px-4 pt-8 md:pt-0">
                    <dt
                        class="order-2 mt-3 text-sm font-bold tracking-widest uppercase text-slate-500 dark:text-slate-400">
                        Professional Workforce</dt>
                    <dd class="order-1 text-5xl font-extrabold text-slate-900 dark:text-white">1,671</dd>
                </div>
                <div class="flex flex-col items-center justify-center px-4 pt-8 md:pt-0">
                    <dt
                        class="order-2 mt-3 text-sm font-bold tracking-widest uppercase text-slate-500 dark:text-slate-400">
                        Hours Without LTI</dt>
                    <dd class="order-1 text-5xl font-extrabold text-slate-900 dark:text-white">5 Million<span
                            class="text-brand-500">+</span></dd>
                </div>
            </div>
        </div>
    </div>

    <section id="about"
        class="py-24 overflow-hidden bg-slate-50 dark:bg-slate-950 lg:py-32 transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid items-center grid-cols-1 gap-16 lg:grid-cols-2">
                <div class="order-2 lg:order-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-0.5 bg-brand-500"></div>
                        <h2 class="text-sm font-bold tracking-widest uppercase text-brand-500">About Us</h2>
                    </div>
                    <h3
                        class="mb-6 text-3xl font-extrabold text-slate-900 dark:text-white sm:text-4xl lg:text-5xl leading-tight">
                        Building the Future of Indonesian Mining
                    </h3>
                    <p class="mb-6 text-lg leading-relaxed text-slate-600 dark:text-slate-400">
                        Established in August 2010, PT. Roda Jaya Sakti has expanded its business presence across
                        Eastern Indonesia. We manage highly potential nickel mining locations with an unwavering
                        commitment to the highest standards of professionalism and sustainability.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10">
                        <div class="p-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
                            <h4 class="text-brand-500 font-bold mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Vision
                            </h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                To be the premier and trusted Mining Services Contractor in Indonesia, providing sustainable added value.
                            </p>
                        </div>
                        <div class="p-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
                            <h4 class="text-brand-500 font-bold mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Mission
                            </h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                Delivering high-quality services with a focus on safety, environment, and operational excellence.
                            </p>
                        </div>
                    </div>
                </div>

                <div x-data="{
                    slide: 0,
                    slides: [
                        '{{ asset('rjs-photos/webp/new-briefing/Salinan_IMG_1545.webp') }}',
                        '{{ asset('rjs-photos/webp/new-briefing/Salinan_IMG_1546.webp') }}',
                        '{{ asset('rjs-photos/webp/new-briefing/Salinan_IMG_1552.webp') }}',
                        '{{ asset('rjs-photos/webp/new-briefing/Salinan_IMG_1553.webp') }}',
                        '{{ asset('rjs-photos/webp/new-briefing/Salinan_IMG_1556.webp') }}',
                    ],
                    init() { setInterval(() => this.slide = (this.slide + 1) % this.slides.length, 5000) }
                }" class="relative order-1 lg:order-2">
                    <div class="relative z-10 w-full overflow-hidden rounded-3xl shadow-2xl aspect-4/3">
                        <template x-for="(src, i) in slides" :key="i">
                            <img :src="src" alt="RJS Team" style="display:none;"
                                x-show="slide === i"
                                x-transition:enter="transition-opacity duration-1000"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition-opacity duration-1000"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="absolute inset-0 object-cover w-full h-full dark:brightness-75 dark:contrast-125">
                        </template>
                        <div class="absolute inset-0 bg-slate-900/10 mix-blend-multiply"></div>
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                            <template x-for="(src, i) in slides" :key="i">
                                <button @click="slide = i"
                                    class="w-1.5 h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                                    :class="slide === i ? 'bg-white w-4' : 'bg-white/50'"></button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section
        class="py-32 bg-white dark:bg-slate-900 border-y border-slate-100 dark:border-slate-800 transition-colors duration-300 overflow-hidden">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col xl:flex-row items-center justify-between gap-16 lg:gap-24">
                <div class="max-w-xl text-center xl:text-left xl:w-1/3">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-brand-500">Standards of Excellence</h2>
                    <h3 class="mt-3 text-4xl font-extrabold text-slate-900 dark:text-white sm:text-5xl leading-tight">
                        International Certifications</h3>
                    <p class="mt-6 text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                        We are fully committed to implementing international standards in superior quality management,
                        responsible environmental management, and occupational health and safety (OHS) practices.
                    </p>
                </div>

                <div
                    class="w-full xl:w-2/3 flex flex-col sm:flex-row justify-center xl:justify-end gap-6 sm:gap-8 items-stretch pt-8 sm:pt-0">
                    <div class="relative group w-full sm:w-1/3 transform translate-y-0 sm:translate-y-6">
                        <div
                            class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-3xl group-hover:bg-brand-500/40 transition-all duration-500 opacity-0 group-hover:opacity-100">
                        </div>
                        <div
                            class="relative h-full bg-white dark:bg-slate-950 p-8 rounded-4xl border border-slate-100 dark:border-slate-800 shadow-xl hover:shadow-2xl transition-all duration-500 transform group-hover:-translate-y-3 flex flex-col items-center justify-between">
                            <img src="{{ asset('rjs-photos/company-iso-(International Organization for Standardization)/ISO9001.jpg') }}"
                                alt="ISO 9001"
                                class="h-40 sm:h-48 w-auto object-contain mix-blend-multiply dark:mix-blend-normal">
                            <div class="mt-8 text-center w-full pt-6 border-t border-slate-100 dark:border-slate-800">
                                <span class="block text-lg font-extrabold text-slate-900 dark:text-white mb-1">ISO
                                    9001:2015</span>
                                <span class="text-sm font-medium text-brand-500">Quality Management</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative group w-full sm:w-1/3 transform translate-y-0 sm:-translate-y-6">
                        <div
                            class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-3xl group-hover:bg-brand-500/40 transition-all duration-500 opacity-0 group-hover:opacity-100">
                        </div>
                        <div
                            class="relative h-full bg-white dark:bg-slate-950 p-8 rounded-4xl border border-slate-100 dark:border-slate-800 shadow-xl hover:shadow-2xl transition-all duration-500 transform group-hover:-translate-y-3 flex flex-col items-center justify-between">
                            <img src="{{ asset('rjs-photos/company-iso-(International Organization for Standardization)/ISO14001.jpg') }}"
                                alt="ISO 14001"
                                class="h-40 sm:h-48 w-auto object-contain mix-blend-multiply dark:mix-blend-normal">
                            <div class="mt-8 text-center w-full pt-6 border-t border-slate-100 dark:border-slate-800">
                                <span class="block text-lg font-extrabold text-slate-900 dark:text-white mb-1">ISO
                                    14001:2015</span>
                                <span class="text-sm font-medium text-brand-500">Environmental</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative group w-full sm:w-1/3 transform translate-y-0 sm:translate-y-6">
                        <div
                            class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-3xl group-hover:bg-brand-500/40 transition-all duration-500 opacity-0 group-hover:opacity-100">
                        </div>
                        <div
                            class="relative h-full bg-white dark:bg-slate-950 p-8 rounded-4xl border border-slate-100 dark:border-slate-800 shadow-xl hover:shadow-2xl transition-all duration-500 transform group-hover:-translate-y-3 flex flex-col items-center justify-between">
                            <img src="{{ asset('rjs-photos/company-iso-(International Organization for Standardization)/ISO45001.jpg') }}"
                                alt="ISO 45001"
                                class="h-40 sm:h-48 w-auto object-contain mix-blend-multiply dark:mix-blend-normal">
                            <div class="mt-8 text-center w-full pt-6 border-t border-slate-100 dark:border-slate-800">
                                <span class="block text-lg font-extrabold text-slate-900 dark:text-white mb-1">ISO
                                    45001:2018</span>
                                <span class="text-sm font-medium text-brand-500">Health & Safety</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="py-24 bg-slate-50 dark:bg-slate-950 lg:py-32 transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center mb-16">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <div class="w-8 h-0.5 bg-brand-500"></div>
                    <h2 class="text-sm font-bold tracking-widest uppercase text-brand-500">What We Do</h2>
                    <div class="w-8 h-0.5 bg-brand-500"></div>
                </div>
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white sm:text-4xl">Integrated &
                    Professional Services</h3>
                <p class="mt-4 text-lg text-slate-600 dark:text-slate-400">High safety standards, operational
                    efficiency, and continuous innovation in every project we handle.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-700 hover:-translate-y-1">
                    <div class="aspect-4/3 overflow-hidden">
                        <img src="{{ asset('rjs-photos/webp/new-hauling-photo/42.webp') }}"
                            alt="Ore Hauling"
                            class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110 dark:brightness-75 dark:contrast-125">
                    </div>
                    <div class="p-6">
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Ore Hauling</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Efficient and secure hauling services
                            for nickel mining operations with a robust fleet.</p>
                    </div>
                </div>

                <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-700 hover:-translate-y-1">
                    <div class="aspect-4/3 overflow-hidden">
                        <img src="{{ asset('rjs-photos/webp/new-hauling-photo/52.webp') }}" alt="Nickel Hauling"
                            class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110 dark:brightness-75 dark:contrast-125">
                    </div>
                    <div class="p-6">
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Nickel Hauling</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Specialized logistics for nickel ore,
                            ensuring timely delivery from pit to port.</p>
                    </div>
                </div>

                <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-700 hover:-translate-y-1">
                    <div class="aspect-4/3 overflow-hidden">
                        <img src="{{ asset('rjs-photos/our-services/Konstruksi.jpg') }}" alt="Construction"
                            class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110 dark:brightness-75 dark:contrast-125">
                    </div>
                    <div class="p-6">
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Construction</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Reliable infrastructure construction
                            supporting mining operations and general projects.</p>
                    </div>
                </div>

                <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-700 hover:-translate-y-1">
                    <div class="aspect-4/3 overflow-hidden">
                        <img src="{{ asset('rjs-photos/our-services/Pembuatan Irigasi.jpg') }}"
                            alt="Irrigation Construction"
                            class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110 dark:brightness-75 dark:contrast-125">
                    </div>
                    <div class="p-6">
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Irrigation Construction</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Building sustainable water management
                            systems and irrigation infrastructure.</p>
                    </div>
                </div>

                <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-700 hover:-translate-y-1">
                    <div class="aspect-4/3 overflow-hidden">
                        <img src="{{ asset('rjs-photos/our-services/Pembuatan Jalan Logging.jpg') }}"
                            alt="Logging Road Construction"
                            class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110 dark:brightness-75 dark:contrast-125">
                    </div>
                    <div class="p-6">
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Logging Road Construction
                        </h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Developing durable access roads for
                            forestry and mining activities.</p>
                    </div>
                </div>

                <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-700 hover:-translate-y-1">
                    <div class="aspect-4/3 overflow-hidden">
                        <img src="{{ asset('rjs-photos/our-services/Pembuatan Jetty.jpg') }}"
                            alt="Jetty Construction"
                            class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110 dark:brightness-75 dark:contrast-125">
                    </div>
                    <div class="p-6">
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Jetty Construction</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Expert engineering and construction of
                            port facilities and jetties.</p>
                    </div>
                </div>

                <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-700 hover:-translate-y-1">
                    <div class="aspect-4/3 overflow-hidden">
                        <img src="{{ asset('rjs-photos/our-services/Pembukaan Lahan Kelapa Sawit.jpg') }}"
                            alt="Palm Oil Land Clearing"
                            class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110 dark:brightness-75 dark:contrast-125">
                    </div>
                    <div class="p-6">
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Land Clearing</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Professional land preparation and
                            clearing for plantations and industrial sites.</p>
                    </div>
                </div>

                <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-700 hover:-translate-y-1">
                    <div class="aspect-4/3 overflow-hidden">
                        <img src="{{ asset('rjs-photos/our-services/Rental Alat Berat.jpg') }}"
                            alt="Heavy Equipment Rental"
                            class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110 dark:brightness-75 dark:contrast-125">
                    </div>
                    <div class="p-6">
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Equipment Rental</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Providing top-tier, well-maintained heavy
                            machinery with skilled operators.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="news" class="py-24 bg-white dark:bg-slate-900 lg:py-32 transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="flex justify-between items-end mb-12">
                <div class="max-w-2xl">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-0.5 bg-brand-500"></div>
                        <h2 class="text-sm font-bold tracking-widest uppercase text-brand-500">Latest Updates</h2>
                    </div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white sm:text-4xl">Company News</h3>
                </div>
                <a href="{{ route('articles.index') }}"
                    class="hidden sm:inline-flex items-center text-brand-500 font-semibold hover:text-brand-600 transition-colors">
                    View All News
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            @if ($latestArticles->isNotEmpty())
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    @foreach ($latestArticles as $article)
                        <a href="{{ route('articles.show', $article) }}" x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                            class="group flex flex-col bg-slate-50 dark:bg-slate-800 rounded-3xl overflow-hidden border border-slate-100 dark:border-slate-700 transition-all duration-700 hover:border-brand-500/30 hover:shadow-xl">
                            <div class="h-48 bg-slate-200 dark:bg-slate-700 relative overflow-hidden">
                                @if ($article->featuredImage)
                                    <img src="{{ Storage::url($article->featuredImage->path) }}"
                                        alt="{{ $article->title }}"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                @else
                                    <div
                                        class="absolute inset-0 flex items-center justify-center text-slate-400 dark:text-slate-500">
                                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5L18.5 7H20a2 2 0 012 2v10a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6 flex-1 flex flex-col">
                                @if ($article->category)
                                    <div
                                        class="flex items-center text-xs font-semibold text-brand-500 mb-3 uppercase tracking-wider">
                                        {{ $article->category }}</div>
                                @endif
                                <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-3 line-clamp-2 group-hover:text-brand-500 transition-colors">
                                    {{ $article->title }}</h4>
                                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed mb-6 flex-1 line-clamp-3">
                                    {{ Str::limit(strip_tags($article->content), 150) }}</p>
                                <span class="text-sm font-medium text-slate-500 dark:text-slate-500">
                                    {{ $article->published_at->format('F j, Y') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 text-slate-400 dark:text-slate-600">
                    <p class="text-lg">No news articles published yet. Check back soon!</p>
                    <a href="{{ route('articles.index') }}"
                        class="mt-4 inline-block text-brand-500 font-semibold hover:text-brand-600">Browse all
                        news →</a>
                </div>
            @endif
        </div>
    </section>

    <section id="jobs" class="py-24 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="relative rounded-3xl overflow-hidden shadow-2xl bg-slate-900 border border-slate-800">
                <div class="absolute inset-0 z-0">
                    <div class="absolute inset-0 bg-brand-500/10 mix-blend-color-dodge"></div>
                    <div
                        class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 w-96 h-96 bg-brand-500/20 rounded-full blur-3xl">
                    </div>
                </div>

                <div
                    class="relative z-10 p-12 lg:p-20 flex flex-col lg:flex-row items-center justify-between text-center lg:text-left">
                    <div class="mb-10 lg:mb-0 max-w-2xl">
                        <h2 class="text-sm font-bold tracking-widest uppercase text-brand-500 mb-4">Career
                            Opportunities</h2>
                        <h3 class="text-3xl font-extrabold text-white sm:text-5xl leading-tight">Ready to build your
                            career with RJS?</h3>
                        <p class="mt-6 text-lg text-slate-300 leading-relaxed">We are constantly seeking top talent to
                            grow with us and shape the future of the Indonesian mining industry. Create your digital
                            profile and discover open roles.</p>

                        <div
                            class="mt-8 inline-block px-6 py-3 rounded-full bg-slate-800/80 border border-slate-700/50 backdrop-blur-sm">
                            <span class="text-lg font-medium text-slate-300">Join <span
                                    class="text-brand-400 font-bold">1,600+</span> professionals</span>
                        </div>
                    </div>
                    <div class="shrink-0 w-full lg:w-auto mt-6 lg:mt-0">
                        <a href="{{ route('careers.index') }}"
                            class="w-full lg:w-auto inline-flex items-center justify-center px-8 py-5 text-lg font-bold text-slate-900 bg-brand-500 rounded-xl hover:bg-brand-400 hover:-translate-y-1 transition-all duration-300 shadow-[0_0_20px_rgba(245,166,35,0.3)] hover:shadow-[0_0_30px_rgba(245,166,35,0.5)]">
                            Enter Career Portal
                            <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
    @livewireScripts
    @fluxScripts
</body>

</html>
