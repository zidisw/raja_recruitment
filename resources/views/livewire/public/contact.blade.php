<div>

    {{-- Page Hero --}}
    <div class="relative bg-slate-900 py-20 lg:py-28 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('rjs-photos/webp/new-briefing/image3.webp') }}" alt="RJS Team"
                class="object-cover w-full h-full opacity-25">
            <div class="absolute inset-0 bg-linear-to-r from-slate-950 via-slate-900/90 to-slate-900/50"></div>
        </div>
        <div class="relative z-10 px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm text-slate-400">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-400 transition-colors">Home</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-brand-400 font-medium">Contact</li>
                </ol>
            </nav>
            <div class="max-w-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-0.5 bg-brand-500"></div>
                    <span class="text-sm font-bold tracking-widest uppercase text-brand-400">Get in Touch</span>
                </div>
                <h1 class="text-4xl font-extrabold text-white sm:text-5xl leading-tight">
                    Contact PT. Roda Jaya Sakti
                </h1>
                <p class="mt-5 text-lg text-slate-300 leading-relaxed">
                    We're ready to help. Send us your questions or business inquiries via the form below, or contact us
                    directly.
                </p>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="py-16 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-12">

                {{-- Left Column: Contact Info + Map --}}
                <div class="lg:w-5/12 space-y-6">

                    {{-- Contact Info Cards --}}
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
                        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Contact Information</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Reach us through the following
                                channels</p>
                        </div>

                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                            {{-- Address --}}
                            <div class="flex items-start gap-4 p-5">
                                <div
                                    class="shrink-0 w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-brand-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">
                                        Office Address</p>
                                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200 leading-relaxed">
                                        Jl. Tupai No 71 A<br>
                                        Makassar, South Sulawesi<br>
                                        Indonesia 90222
                                    </p>
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="flex items-start gap-4 p-5">
                                <div
                                    class="shrink-0 w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-brand-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">
                                        Phone</p>
                                    <a href="tel:+624118050663"
                                        class="text-sm font-medium text-slate-800 dark:text-slate-200 hover:text-brand-500 transition-colors">
                                        (0411) 850663
                                    </a>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="flex items-start gap-4 p-5">
                                <div
                                    class="shrink-0 w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-brand-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">
                                        Email</p>
                                    <a href="mailto:info@rodajayasakti.id"
                                        class="text-sm font-medium text-slate-800 dark:text-slate-200 hover:text-brand-500 transition-colors">
                                        info@rodajayasakti.id
                                    </a>
                                </div>
                            </div>

                            {{-- Office Hours --}}
                            <div class="flex items-start gap-4 p-5">
                                <div
                                    class="shrink-0 w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-brand-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">
                                        Office Hours</p>
                                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200">
                                        Monday – Friday<br>
                                        08:00 – 17:00 WITA
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Map --}}
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
                        <div class="p-5 border-b border-slate-100 dark:border-slate-800">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Our Location</h3>
                        </div>
                        <div class="relative">
                            <iframe
                                src="https://www.openstreetmap.org/export/embed.html?bbox=119.41510242940037%2C-5.16623427663862%2C119.42510242940037%2C-5.15623427663862&layer=mapnik&marker=-5.16123427663862%2C119.42010242940037"
                                class="w-full h-72 border-0" allowfullscreen="" loading="lazy"
                                title="PT. Roda Jaya Sakti Location">
                            </iframe>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50">
                            <a href="https://www.openstreetmap.org/?mlat=-5.16123427663862&mlon=119.42010242940037#map=17/-5.16123427663862/119.42010242940037"
                                target="_blank" rel="noopener noreferrer"
                                class="text-xs text-brand-500 hover:text-brand-600 font-medium flex items-center gap-1 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Open in OpenStreetMap
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Contact Form --}}
                <div class="lg:flex-1">
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Send a Message</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Fill in the form below and we'll
                                get back to you as soon as possible.</p>
                        </div>

                        <div class="p-6 lg:p-8">

                            {{-- Success State --}}
                            @if ($submitted)
                                <div class="flex flex-col items-center justify-center text-center py-12 px-4">
                                    <div
                                        class="w-16 h-16 rounded-full bg-green-100 dark:bg-green-500/10 flex items-center justify-center mb-5">
                                        <svg class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Message Sent!</h3>
                                    <p class="text-slate-500 dark:text-slate-400 text-sm max-w-sm">
                                        Thank you, <span
                                            class="font-semibold text-slate-700 dark:text-slate-300">{{ $name }}</span>.
                                        Your message has been received and our team will contact you shortly.
                                    </p>
                                    <button wire:click="$set('submitted', false)"
                                        class="mt-6 text-sm font-semibold text-brand-500 hover:text-brand-600 transition-colors cursor-pointer">
                                        Send another message →
                                    </button>
                                </div>

                                {{-- Form --}}
                            @else
                                {{-- Error Message --}}
                                @if ($errorMessage)
                                    <div
                                        class="mb-6 flex items-start gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                                        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-sm text-red-700 dark:text-red-400">{{ $errorMessage }}</p>
                                    </div>
                                @endif

                                <form wire:submit="sendMessage" class="space-y-5">
                                    {{-- Name & Email --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label for="contact-name"
                                                class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                                Full Name <span class="text-red-500">*</span>
                                            </label>
                                            <input wire:model="name" id="contact-name" type="text" placeholder="Your name"
                                                class="w-full px-4 py-2.5 rounded-xl border text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/50
                                                        {{ $errors->has('name') ? 'border-red-400 bg-red-50 dark:bg-red-900/10 text-red-900 dark:text-red-300' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white' }}">
                                            @error('name')
                                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="contact-email"
                                                class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                                Email <span class="text-red-500">*</span>
                                            </label>
                                            <input wire:model="email" id="contact-email" type="email"
                                                placeholder="your@email.com"
                                                class="w-full px-4 py-2.5 rounded-xl border text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/50
                                                        {{ $errors->has('email') ? 'border-red-400 bg-red-50 dark:bg-red-900/10 text-red-900 dark:text-red-300' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white' }}">
                                            @error('email')
                                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Phone & Subject --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label for="contact-phone"
                                                class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                                Phone Number <span class="text-slate-400 font-normal">(optional)</span>
                                            </label>
                                            <input wire:model="phone" id="contact-phone" type="tel"
                                                placeholder="+62 812 3456 7890"
                                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
                                        </div>

                                        <div>
                                            <label for="contact-subject"
                                                class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                                Subject <span class="text-red-500">*</span>
                                            </label>
                                            <input wire:model="subject" id="contact-subject" type="text"
                                                placeholder="Subject of your message"
                                                class="w-full px-4 py-2.5 rounded-xl border text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/50
                                                        {{ $errors->has('subject') ? 'border-red-400 bg-red-50 dark:bg-red-900/10 text-red-900 dark:text-red-300' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white' }}">
                                            @error('subject')
                                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Message --}}
                                    <div>
                                        <label for="contact-message"
                                            class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                            Message <span class="text-red-500">*</span>
                                        </label>
                                        <textarea wire:model="message" id="contact-message" rows="6"
                                            placeholder="Write your message here..."
                                            class="w-full px-4 py-2.5 rounded-xl border text-sm resize-none transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/50
                                                    {{ $errors->has('message') ? 'border-red-400 bg-red-50 dark:bg-red-900/10 text-red-900 dark:text-red-300' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white' }}"></textarea>
                                        @error('message')
                                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Submit --}}
                                    <div class="flex items-center justify-between pt-2">
                                        <p class="text-xs text-slate-400">
                                            <span class="text-red-500">*</span> Required
                                        </p>
                                        <button type="submit" wire:loading.attr="disabled"
                                            class="inline-flex items-center gap-2 px-8 py-3 text-sm font-bold text-white bg-brand-500 hover:bg-brand-600 disabled:opacity-60 disabled:cursor-not-allowed rounded-full transition-all hover:shadow-lg hover:shadow-brand-500/30 cursor-pointer">
                                            <span wire:loading.remove wire:target="sendMessage">Send Message</span>
                                            <span wire:loading wire:target="sendMessage">Sending...</span>
                                            <svg wire:loading.remove wire:target="sendMessage" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                            </svg>
                                            <svg wire:loading wire:target="sendMessage" class="w-4 h-4 animate-spin"
                                                fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                            @endif

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>