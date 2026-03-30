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
                    <li><a href="{{ route('about') }}" class="hover:text-brand-500 transition-colors">About Us</a></li>
                    <li><a href="{{ route('careers.index') }}" class="hover:text-brand-500 transition-colors">Career
                            Portal</a></li>
                    <li><a href="{{ route('articles.index') }}" class="hover:text-brand-500 transition-colors">Company
                            News</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-brand-500 transition-colors">Contact</a></li>
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