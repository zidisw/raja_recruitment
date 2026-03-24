@extends('layouts.public')

@section('title', 'About Us')
@section('meta_description', "Learn about PT. Roda Jaya Sakti — established 2010, Eastern Indonesia's leading mining services contractor with 641+ heavy equipment units and 1,671 professionals.")

@section('content')

    {{-- Hero Section --}}
    <div x-data="{ slide: 0, init() { setInterval(() => this.slide = (this.slide + 1) % 4, 6000) } }"
        class="relative bg-slate-900 py-24 lg:py-36 overflow-hidden">
        <div class="absolute inset-0 z-0 overflow-hidden">
            @foreach ([
                    'rjs-photos/webp/new-tampak-atas/10.webp',
                    'rjs-photos/webp/new-tampak-atas/image6.webp',
                    'rjs-photos/webp/new-tampak-atas/353.webp',
                    'rjs-photos/webp/new-tampak-atas/image7.webp',
                ] as $idx => $img)
                    <img src="{{ asset($img) }}" alt="RJS Mining Operations" style="display:none;"
                        x-show="slide === {{ $idx }}"
                        x-transition:enter="transition-opacity duration-[1500ms]"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-50"
                        x-transition:leave="transition-opacity duration-[1500ms]"
                        x-transition:leave-start="opacity-50"
                        x-transition:leave-end="opacity-0"
                        class="absolute inset-0 object-cover w-full h-full">
            @endforeach
            <div class="absolute inset-0 bg-linear-to-r from-slate-950 via-slate-900/80 to-slate-900/40"></div>
        </div>
        <div class="relative z-10 px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-0.5 bg-brand-500"></div>
                <span class="text-sm font-bold tracking-widest uppercase text-brand-400">Who We Are</span>
            </div>
            <h1 class="text-4xl font-extrabold text-white sm:text-5xl lg:text-6xl leading-tight max-w-3xl">
                About PT. Roda Jaya Sakti
            </h1>
            <p class="mt-6 text-lg text-slate-300 max-w-2xl leading-relaxed">
                A trusted mining services contractor committed to operational excellence, safety, and sustainable growth
                across Eastern Indonesia since 2010.
            </p>
            <nav class="flex mt-8" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm text-slate-400">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-400 transition-colors">Home</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-brand-400 font-medium">About Us</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Company Overview --}}
    <section class="py-24 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid items-center grid-cols-1 gap-16 lg:grid-cols-2">
                <div class="order-2 lg:order-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-0.5 bg-brand-500"></div>
                        <h2 class="text-sm font-bold tracking-widest uppercase text-brand-500">Our Story</h2>
                    </div>
                    <h3 class="mb-6 text-3xl font-extrabold text-slate-900 dark:text-white sm:text-4xl leading-tight">
                        Building the Future of Indonesian Mining
                    </h3>
                    <p class="mb-4 text-lg leading-relaxed text-slate-600 dark:text-slate-400">
                        Established in August 2010, PT. Roda Jaya Sakti (RJS) has grown from a regional contractor into one
                        of Eastern Indonesia's most trusted mining services companies. Headquartered in Makassar, South
                        Sulawesi, we have expanded our operational presence across multiple strategic sites.
                    </p>
                    <p class="mb-4 text-base leading-relaxed text-slate-600 dark:text-slate-400">
                        We manage highly potential nickel mining locations and provide a comprehensive suite of services
                        spanning ore hauling, heavy equipment rental, infrastructure construction, land clearing, jetty
                        construction, and irrigation systems.
                    </p>
                    <p class="text-base leading-relaxed text-slate-600 dark:text-slate-400">
                        Our unwavering commitment to the highest standards of professionalism, safety, and environmental
                        responsibility has earned us the trust of major partners across the Indonesian mining industry.
                    </p>

                    <div class="mt-10 grid grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-extrabold text-slate-900 dark:text-white">641<span
                                    class="text-brand-500">+</span></div>
                            <div class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-wide">
                                Equipment Units</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-extrabold text-slate-900 dark:text-white">1,671</div>
                            <div class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-wide">
                                Professionals</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-extrabold text-slate-900 dark:text-white">5M<span
                                    class="text-brand-500">+</span></div>
                            <div class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-wide">
                                LTI-Free Hours</div>
                        </div>
                    </div>
                </div>

                <div x-data="{
                    slide: 0,
                    slides: [
                        '{{ asset('rjs-photos/webp/new-briefing/image3.webp') }}',
                        '{{ asset('rjs-photos/webp/new-briefing/Salinan_IMG_1542.webp') }}',
                        '{{ asset('rjs-photos/webp/new-briefing/Salinan_IMG_1554.webp') }}',
                        '{{ asset('rjs-photos/webp/new-briefing/Salinan_IMG_1558.webp') }}',
                        '{{ asset('rjs-photos/webp/new-briefing/Salinan_IMG_1562.webp') }}',
                        '{{ asset('rjs-photos/webp/new-briefing/Salinan_IMG_1564.webp') }}',
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
                                class="absolute inset-0 object-cover w-full h-full">
                        </template>
                        <div class="absolute inset-0 bg-slate-900/10 mix-blend-multiply"></div>
                        {{-- Slide indicators --}}
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                            <template x-for="(src, i) in slides" :key="i">
                                <button @click="slide = i"
                                    class="w-1.5 h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                                    :class="slide === i ? 'bg-white w-4' : 'bg-white/50'"></button>
                            </template>
                        </div>
                    </div>
                    <div
                        class="absolute -bottom-6 -left-6 z-20 bg-brand-500 text-white rounded-2xl p-6 shadow-2xl shadow-brand-500/30">
                        <div class="text-3xl font-black">14+</div>
                        <div class="text-sm font-semibold mt-1 text-brand-100">Years of Excellence</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Vision & Mission --}}
    <section class="py-24 bg-white dark:bg-slate-900 transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center mb-16">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <div class="w-8 h-0.5 bg-brand-500"></div>
                    <h2 class="text-sm font-bold tracking-widest uppercase text-brand-500">Our Direction</h2>
                    <div class="w-8 h-0.5 bg-brand-500"></div>
                </div>
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white sm:text-4xl">Vision & Mission</h3>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                {{-- Vision --}}
                <div
                    class="relative overflow-hidden rounded-3xl bg-slate-900 dark:bg-slate-800 p-10 border border-slate-800 dark:border-slate-700">
                    <div
                        class="absolute top-0 right-0 w-48 h-48 bg-brand-500/10 rounded-full blur-3xl -translate-y-12 translate-x-12">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center justify-center w-12 h-12 rounded-2xl bg-brand-500/20">
                                <svg class="w-6 h-6 text-brand-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold text-white">Our Vision</h4>
                        </div>
                        <p class="text-slate-300 text-lg leading-relaxed font-medium">
                            "Menjadi Kontraktor Jasa Pertambangan yang terkemuka, terpercaya, dan berkelanjutan di
                            Indonesia."
                        </p>
                        <p class="mt-4 text-slate-400 text-sm leading-relaxed">
                            To become the foremost, trusted, and sustainable mining services contractor in Indonesia —
                            delivering operational excellence that creates lasting value for our partners, employees, and
                            communities.
                        </p>
                    </div>
                </div>

                {{-- Mission --}}
                <div
                    class="relative overflow-hidden rounded-3xl bg-slate-50 dark:bg-slate-950 p-10 border border-slate-200 dark:border-slate-800">
                    <div
                        class="absolute top-0 right-0 w-48 h-48 bg-brand-500/5 rounded-full blur-3xl -translate-y-12 translate-x-12">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center justify-center w-12 h-12 rounded-2xl bg-brand-500/10">
                                <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold text-slate-900 dark:text-white">Our Mission</h4>
                        </div>
                        <ul class="space-y-4">
                            @foreach ([
                                    'Providing high-quality mining services with the highest safety standards (HSE) and operational efficiency.',
                                    'Developing competent, professional, and welfare-oriented human resources.',
                                    'Implementing environmentally responsible operational practices.',
                                    'Building strategic and mutually beneficial partnerships with clients and stakeholders.',
                                    'Creating quality employment opportunities for local communities.',
                                ] as $mission)
                                    <li class="flex items-start gap-3">
                                        <div
                                            class="shrink-0 mt-0.5 flex items-center justify-center w-5 h-5 rounded-full bg-brand-500/20 text-brand-500">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $mission }}
                                        </p>
                                    </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Certifications --}}
    <section class="py-24 bg-slate-50 dark:bg-slate-950 border-y border-slate-100 dark:border-slate-800 transition-colors duration-300 overflow-hidden">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col xl:flex-row items-center justify-between gap-16">
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
                    @foreach ([
                            ['file' => 'ISO9001.jpg', 'name' => 'ISO 9001:2015', 'label' => 'Quality Management', 'offset' => 'sm:translate-y-6'],
                            ['file' => 'ISO14001.jpg', 'name' => 'ISO 14001:2015', 'label' => 'Environmental', 'offset' => 'sm:-translate-y-6'],
                            ['file' => 'ISO45001.jpg', 'name' => 'ISO 45001:2018', 'label' => 'Health & Safety', 'offset' => 'sm:translate-y-6'],
                        ] as $iso)
                            <div class="relative group w-full sm:w-1/3 transform translate-y-0 {{ $iso['offset'] }}">
                                <div
                                    class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-3xl group-hover:bg-brand-500/40 transition-all duration-500 opacity-0 group-hover:opacity-100">
                                </div>
                                <div
                                    class="relative h-full bg-white dark:bg-slate-900 p-8 rounded-4xl border border-slate-100 dark:border-slate-800 shadow-xl hover:shadow-2xl transition-all duration-500 transform group-hover:-translate-y-3 flex flex-col items-center justify-between">
                                    <img src="{{ asset('rjs-photos/company-iso-(International Organization for Standardization)/' . $iso['file']) }}"
                                        alt="{{ $iso['name'] }}"
                                        class="h-40 sm:h-48 w-auto object-contain mix-blend-multiply dark:mix-blend-normal">
                                    <div class="mt-8 text-center w-full pt-6 border-t border-slate-100 dark:border-slate-800">
                                        <span
                                            class="block text-lg font-extrabold text-slate-900 dark:text-white mb-1">{{ $iso['name'] }}</span>
                                        <span class="text-sm font-medium text-brand-500">{{ $iso['label'] }}</span>
                                    </div>
                                </div>
                            </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Our Clients --}}
    <style>
        @keyframes marquee {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }
        .animate-marquee {
            animation: marquee 35s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }
    </style>
    <section class="py-16 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 transition-colors duration-300 overflow-hidden">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8 mb-10 text-center">
            <div class="flex items-center justify-center gap-3 mb-3">
                <div class="w-8 h-0.5 bg-brand-500"></div>
                <h2 class="text-sm font-bold tracking-widest uppercase text-brand-500">Trusted Partners</h2>
                <div class="w-8 h-0.5 bg-brand-500"></div>
            </div>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">Our Clients</h3>
        </div>
        <div class="overflow-hidden">
            <div class="flex gap-6 animate-marquee w-max">
                @php
                    $clientImages = [
                        'WhatsApp Image 2026-03-09 at 15.42.09 (1).jpeg',
                        'WhatsApp Image 2026-03-09 at 15.42.09.jpeg',
                        'WhatsApp Image 2026-03-09 at 15.42.10.jpeg',
                        'WhatsApp Image 2026-03-09 at 15.42.11 (1).jpeg',
                        'WhatsApp Image 2026-03-09 at 15.42.11.jpeg',
                        'WhatsApp Image 2026-03-09 at 15.42.12 (1).jpeg',
                        'WhatsApp Image 2026-03-09 at 15.42.12.jpeg',
                        'WhatsApp Image 2026-03-09 at 15.42.13 (1).jpeg',
                    ];
                    $doubleLoop = array_merge($clientImages, $clientImages);
                @endphp
                @foreach ($doubleLoop as $clientImg)
                    <div class="shrink-0 h-24 w-48 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-3 flex items-center justify-center">
                        <img src="{{ asset('rjs-photos/our-client/' . $clientImg) }}"
                            alt="Client"
                            class="max-h-full max-w-full object-contain">
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Services --}}
    <section class="py-24 bg-white dark:bg-slate-900 transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center mb-16">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <div class="w-8 h-0.5 bg-brand-500"></div>
                    <h2 class="text-sm font-bold tracking-widest uppercase text-brand-500">What We Do</h2>
                    <div class="w-8 h-0.5 bg-brand-500"></div>
                </div>
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white sm:text-4xl">Our Services</h3>
                <p class="mt-4 text-lg text-slate-600 dark:text-slate-400">High safety standards, operational efficiency,
                    and continuous innovation in every project we handle.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                        ['img' => '../webp/new-hauling-photo/42.webp', 'title' => 'Ore Hauling', 'desc' => 'Efficient and secure hauling services for nickel mining operations with a robust fleet.'],
                        ['img' => '../webp/new-hauling-photo/52.webp', 'title' => 'Nickel Hauling', 'desc' => 'Specialized logistics for nickel ore, ensuring timely delivery from pit to port.'],
                        ['img' => 'Konstruksi.jpg', 'title' => 'Construction', 'desc' => 'Reliable infrastructure construction supporting mining operations and general projects.'],
                        ['img' => 'Pembuatan Irigasi.jpg', 'title' => 'Irrigation Construction', 'desc' => 'Building sustainable water management systems and irrigation infrastructure.'],
                        ['img' => 'Pembuatan Jalan Logging.jpg', 'title' => 'Logging Road Construction', 'desc' => 'Developing durable access roads for forestry and mining activities.'],
                        ['img' => 'Pembuatan Jetty.jpg', 'title' => 'Jetty Construction', 'desc' => 'Expert engineering and construction of port facilities and jetties.'],
                        ['img' => 'Pembukaan Lahan Kelapa Sawit.jpg', 'title' => 'Land Clearing', 'desc' => 'Professional land preparation and clearing for plantations and industrial sites.'],
                        ['img' => 'Rental Alat Berat.jpg', 'title' => 'Equipment Rental', 'desc' => 'Providing top-tier, well-maintained heavy machinery with skilled operators.'],
                    ] as $service)
                        <div
                            class="group relative overflow-hidden rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="aspect-4/3 overflow-hidden">
                                <img src="{{ asset('rjs-photos/our-services/' . $service['img']) }}"
                                    alt="{{ $service['title'] }}"
                                    class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110">
                            </div>
                            <div class="p-5">
                                <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1">{{ $service['title'] }}
                                </h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $service['desc'] }}</p>
                            </div>
                        </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Project Gallery --}}
    <section class="py-24 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center mb-16">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <div class="w-8 h-0.5 bg-brand-500"></div>
                    <h2 class="text-sm font-bold tracking-widest uppercase text-brand-500">Our Work</h2>
                    <div class="w-8 h-0.5 bg-brand-500"></div>
                </div>
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white sm:text-4xl">Project Gallery</h3>
                <p class="mt-4 text-lg text-slate-600 dark:text-slate-400">A glimpse into our operations across Eastern
                    Indonesia.</p>
            </div>

            <div x-data="{ lightbox: null }" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach ([
                        ['src' => 'rjs-photos/webp/new-tampak-atas/8.webp', 'alt' => 'Mining Site Aerial View'],
                        ['src' => 'rjs-photos/webp/new-tampak-atas/9.webp', 'alt' => 'Mining Operations Aerial'],
                        ['src' => 'rjs-photos/webp/new-tampak-atas/10.webp', 'alt' => 'Mining Site Overview'],
                        ['src' => 'rjs-photos/webp/new-tampak-atas/11.webp', 'alt' => 'Mining Site Panorama'],
                        ['src' => 'rjs-photos/webp/new-tampak-atas/image4.webp', 'alt' => 'Project Aerial View'],
                        ['src' => 'rjs-photos/webp/new-tampak-atas/image5.webp', 'alt' => 'Mining Operations'],
                        ['src' => 'rjs-photos/webp/new-tampak-atas/image6.webp', 'alt' => 'Site Infrastructure'],
                        ['src' => 'rjs-photos/webp/new-tampak-atas/image7.webp', 'alt' => 'Project Site'],
                        ['src' => 'rjs-photos/webp/new-briefing/image3.webp', 'alt' => 'Team Briefing'],
                        ['src' => 'rjs-photos/webp/new-briefing/Salinan_IMG_1542.webp', 'alt' => 'Team Activity'],
                        ['src' => 'rjs-photos/webp/new-briefing/Salinan_IMG_1544.webp', 'alt' => 'Team Meeting'],
                        ['src' => 'rjs-photos/webp/new-alat-berat/55.webp', 'alt' => 'Heavy Equipment'],
                    ] as $photo)
                        <div @click="lightbox = '{{ asset($photo['src']) }}'"
                            class="group relative aspect-square overflow-hidden rounded-xl cursor-pointer bg-slate-200 dark:bg-slate-800">
                            <img src="{{ asset($photo['src']) }}" alt="{{ $photo['alt'] }}"
                                class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110">
                            <div
                                class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/40 transition-colors duration-300 flex items-center justify-center">
                                <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                </svg>
                            </div>
                        </div>
                @endforeach

                {{-- Lightbox --}}
                <div x-show="lightbox" x-transition.opacity style="display:none;"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
                    @click="lightbox = null" @keydown.escape.window="lightbox = null">
                    <img :src="lightbox" class="max-w-full max-h-full rounded-xl shadow-2xl object-contain"
                        @click.stop>
                    <button @click="lightbox = null"
                        class="absolute top-4 right-4 text-white bg-white/10 hover:bg-white/20 rounded-full p-2 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Leadership --}}
    <section class="py-24 bg-white dark:bg-slate-900 transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center mb-16">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <div class="w-8 h-0.5 bg-brand-500"></div>
                    <h2 class="text-sm font-bold tracking-widest uppercase text-brand-500">Our Leadership</h2>
                    <div class="w-8 h-0.5 bg-brand-500"></div>
                </div>
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white sm:text-4xl">Meet the Leadership Team</h3>
                <p class="mt-4 text-lg text-slate-600 dark:text-slate-400">The visionary leadership driving RJS forward
                    since day one.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                @foreach ([
                        [
                            'photo' => 'ThenringJokkie.png',
                            'name' => 'Thenring Jokkie',
                            'title' => 'President Commissioner',
                            'desc' => 'Leading the oversight function and ensuring the company direction aligns with its vision and principles of good corporate governance.',
                        ],
                        [
                            'photo' => 'ChristineThenring.png',
                            'name' => 'Christine Thenring',
                            'title' => 'Commissioner',
                            'desc' => 'Providing strategic oversight and direction to ensure the company operates in accordance with its vision and good governance principles.',
                        ],
                        [
                            'photo' => 'SoeriantoSoewardi.png',
                            'name' => 'Soerianto Soewardi',
                            'title' => 'President Director',
                            'desc' => 'Leading the execution of the company\'s strategy and operations to sustainably achieve the vision and goals of PT. Roda Jaya Sakti.',
                        ],
                        [
                            'photo' => 'AgungThenring.png',
                            'name' => 'Agung Thenring',
                            'title' => 'Vice President Director',
                            'desc' => 'Supporting the President Director in implementing strategic policies and ensuring operational activities run effectively and efficiently.',
                        ],
                        [
                            'photo' => 'JuniorThenring.png',
                            'name' => 'Junior Thenring',
                            'title' => 'Non-Executive Director',
                            'desc' => 'Providing independent perspective and strategic oversight to ensure company management adheres to principles of safety, efficiency, and good governance.',
                        ],
                    ] as $leader)
                        <div class="group flex flex-col overflow-hidden rounded-3xl bg-slate-900 dark:bg-slate-800 border border-slate-800 dark:border-slate-700 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                            <div class="aspect-3/4 overflow-hidden">
                                <img src="{{ asset('rjs-photos/new-owner/' . $leader['photo']) }}"
                                    alt="{{ $leader['name'] }}"
                                    class="object-cover object-top w-full h-full transition-transform duration-700 group-hover:scale-105">
                            </div>
                            <div class="p-5 flex flex-col flex-1 border-t-4 border-brand-500">
                                <h4 class="text-base font-extrabold text-white leading-tight">{{ $leader['name'] }}</h4>
                                <p class="text-brand-400 font-semibold text-xs mt-1">{{ $leader['title'] }}</p>
                                <p class="text-slate-400 text-xs mt-3 leading-relaxed flex-1">{{ $leader['desc'] }}</p>
                            </div>
                        </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Contact CTA --}}
    <section class="py-20 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="relative rounded-3xl overflow-hidden shadow-2xl bg-slate-900 border border-slate-800">
                <div class="absolute inset-0 z-0">
                    <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 w-96 h-96 bg-brand-500/20 rounded-full blur-3xl"></div>
                </div>
                <div class="relative z-10 p-12 lg:p-20 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-3xl font-extrabold text-white sm:text-4xl leading-tight">Interested in
                            partnering with RJS?</h2>
                        <p class="mt-4 text-lg text-slate-300">Get in touch with our team to discuss your project
                            requirements and how we can support your operations.</p>
                    </div>
                    <div class="space-y-5">
                        <div class="flex items-start gap-4">
                            <div class="shrink-0 w-10 h-10 rounded-xl bg-brand-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-white">Address</p>
                                <p class="text-sm text-slate-400 mt-0.5">Jl. Tupai No 71 A, Makassar, South Sulawesi</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="shrink-0 w-10 h-10 rounded-xl bg-brand-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-white">Phone</p>
                                <p class="text-sm text-slate-400 mt-0.5">(0411) 850663</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="shrink-0 w-10 h-10 rounded-xl bg-brand-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-white">Email</p>
                                <p class="text-sm text-slate-400 mt-0.5">info@rodajayasakti.id</p>
                            </div>
                        </div>
                        <div class="pt-2">
                            <a href="{{ route('careers.index') }}"
                                class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-slate-900 bg-brand-500 rounded-xl hover:bg-brand-400 transition-all duration-300 hover:-translate-y-0.5">
                                View Career Opportunities
                                <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
