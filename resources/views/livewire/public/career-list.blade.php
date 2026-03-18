<div>

    {{-- Page Hero --}}
    <div class="relative bg-slate-900 py-20 lg:py-28 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('rjs-photos/webp/new-tampak-atas/42.webp') }}" alt="RJS Mining Operations"
                class="object-cover w-full h-full opacity-30">
            <div class="absolute inset-0 from-slate-950 via-slate-900/90 to-slate-900/50 bg-[linear-gradient(to_right,var(--tw-gradient-stops))]"></div>
        </div>
        <div class="relative z-10 px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm text-slate-400">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-400 transition-colors">Home</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-brand-400 font-medium">Careers</li>
                </ol>
            </nav>
            <div class="max-w-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-0.5 bg-brand-500"></div>
                    <span class="text-sm font-bold tracking-widest uppercase text-brand-400">Join Our Team</span>
                </div>
                <h1 class="text-4xl font-extrabold text-white sm:text-5xl leading-tight">
                    Career Opportunities at RJS
                </h1>
                <p class="mt-5 text-lg text-slate-300 leading-relaxed">
                    We are constantly seeking top talent to grow with us and shape the future of the Indonesian mining
                    industry.
                </p>
                <div class="mt-8 inline-flex items-center px-5 py-3 rounded-full bg-slate-800/80 border border-slate-700/50 backdrop-blur-sm">
                    <span class="text-base font-medium text-slate-300">Join <span class="text-brand-400 font-bold">1,600+</span> professionals</span>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-10">

                {{-- Main Content --}}
                <div class="flex-1 min-w-0">

                    {{-- Filter Bar --}}
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-5 mb-8 shadow-sm">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="relative flex-1">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search job title..."
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
                            </div>
                            {{-- Department Dropdown --}}
                            <div x-data="{
                                open: false,
                                options: { '': 'All Departments', ...{{ Js::from($departments->pluck('name', 'id')) }} },
                                get label() { return this.options[$wire.department] ?? 'All Departments'; },
                                select(val) { $wire.set('department', val); this.open = false; }
                            }" @click.away="open = false" @keydown.escape.window="open = false" class="relative min-w-36">
                                <button @click="open = !open" type="button"
                                    class="flex items-center justify-between w-full px-4 py-2.5 rounded-xl border text-sm transition-all duration-200 cursor-pointer"
                                    :class="open ? 'ring-2 ring-brand-500/50 border-brand-300 dark:border-brand-600 bg-white dark:bg-slate-800' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600'">
                                    <span class="truncate" :class="$wire.department ? 'text-slate-900 dark:text-white font-medium' : 'text-slate-500 dark:text-slate-400'" x-text="label"></span>
                                    <svg class="w-4 h-4 ml-2 text-slate-400 shrink-0 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-1"
                                    class="absolute z-50 mt-2 w-full min-w-max rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50 overflow-auto max-h-60 py-1">
                                    <button type="button" @click="select('')"
                                        class="w-full text-left px-4 py-2 text-sm transition-colors flex items-center justify-between gap-3"
                                        :class="!$wire.department ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                        <span>All Departments</span>
                                        <svg x-show="!$wire.department" class="w-4 h-4 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    @foreach ($departments as $dept)
                                        <button type="button" @click="select('{{ $dept->id }}')"
                                            class="w-full text-left px-4 py-2 text-sm transition-colors flex items-center justify-between gap-3"
                                            :class="$wire.department == '{{ $dept->id }}' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                            <span>{{ $dept->name }}</span>
                                            <svg x-show="$wire.department == '{{ $dept->id }}'" class="w-4 h-4 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Site Dropdown --}}
                            <div x-data="{
                                open: false,
                                options: { '': 'All Sites', ...{{ Js::from($sites->pluck('name', 'id')) }} },
                                get label() { return this.options[$wire.site] ?? 'All Sites'; },
                                select(val) { $wire.set('site', val); this.open = false; }
                            }" @click.away="open = false" @keydown.escape.window="open = false" class="relative min-w-32">
                                <button @click="open = !open" type="button"
                                    class="flex items-center justify-between w-full px-4 py-2.5 rounded-xl border text-sm transition-all duration-200 cursor-pointer"
                                    :class="open ? 'ring-2 ring-brand-500/50 border-brand-300 dark:border-brand-600 bg-white dark:bg-slate-800' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600'">
                                    <span class="truncate" :class="$wire.site ? 'text-slate-900 dark:text-white font-medium' : 'text-slate-500 dark:text-slate-400'" x-text="label"></span>
                                    <svg class="w-4 h-4 ml-2 text-slate-400 shrink-0 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-1"
                                    class="absolute z-50 mt-2 w-full min-w-max rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50 overflow-auto max-h-60 py-1">
                                    <button type="button" @click="select('')"
                                        class="w-full text-left px-4 py-2 text-sm transition-colors flex items-center justify-between gap-3"
                                        :class="!$wire.site ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                        <span>All Sites</span>
                                        <svg x-show="!$wire.site" class="w-4 h-4 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    @foreach ($sites as $s)
                                        <button type="button" @click="select('{{ $s->id }}')"
                                            class="w-full text-left px-4 py-2 text-sm transition-colors flex items-center justify-between gap-3"
                                            :class="$wire.site == '{{ $s->id }}' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                            <span>{{ $s->name }}</span>
                                            <svg x-show="$wire.site == '{{ $s->id }}'" class="w-4 h-4 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Level Dropdown --}}
                            <div x-data="{
                                open: false,
                                options: { '': 'All Levels', 'staff': 'Staff', 'non_staff': 'Non-Staff' },
                                get label() { return this.options[$wire.level] ?? 'All Levels'; },
                                select(val) { $wire.set('level', val); this.open = false; }
                            }" @click.away="open = false" @keydown.escape.window="open = false" class="relative min-w-28">
                                <button @click="open = !open" type="button"
                                    class="flex items-center justify-between w-full px-4 py-2.5 rounded-xl border text-sm transition-all duration-200 cursor-pointer"
                                    :class="open ? 'ring-2 ring-brand-500/50 border-brand-300 dark:border-brand-600 bg-white dark:bg-slate-800' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600'">
                                    <span class="truncate" :class="$wire.level ? 'text-slate-900 dark:text-white font-medium' : 'text-slate-500 dark:text-slate-400'" x-text="label"></span>
                                    <svg class="w-4 h-4 ml-2 text-slate-400 shrink-0 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-1"
                                    class="absolute z-50 mt-2 w-full min-w-max rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50 overflow-auto max-h-60 py-1">
                                    <template x-for="(lbl, val) in options" :key="val">
                                        <button type="button" @click="select(val)"
                                            class="w-full text-left px-4 py-2 text-sm transition-colors flex items-center justify-between gap-3"
                                            :class="$wire.level == val ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                            <span x-text="lbl"></span>
                                            <svg x-show="$wire.level == val" class="w-4 h-4 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Job Count --}}
                    <div class="mb-5 flex items-center justify-between">
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $jobs->count() }}</span>
                            {{ Str::plural('position', $jobs->count()) }} available
                        </p>
                        <div wire:loading class="flex items-center gap-2 text-xs text-slate-400">
                            <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Filtering...
                        </div>
                    </div>

                    {{-- Job Listings --}}
                    @if ($jobs->isEmpty())
                        <div class="flex flex-col items-center justify-center py-20 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300 mb-1">No positions found</h3>
                            <p class="text-slate-400 text-sm">Try adjusting your filters or check back later.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($jobs as $job)
                                <a href="{{ route('careers.show', $job) }}" wire:key="{{ $job->id }}"
                                    class="group flex flex-col sm:flex-row gap-5 items-start sm:items-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-5 shadow-sm hover:shadow-lg hover:border-brand-200 dark:hover:border-brand-800 transition-all duration-300">

                                    {{-- Featured Image or Icon --}}
                                    <div class="w-full sm:w-20 h-32 sm:h-16 rounded-xl overflow-hidden shrink-0 bg-slate-100 dark:bg-slate-800">
                                        @if ($job->featuredImage)
                                            <img src="{{ Storage::url($job->featuredImage->path) }}" alt="{{ $job->title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Job Info --}}
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-brand-500 transition-colors">
                                            {{ $job->title }}
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 text-sm text-slate-400">
                                            @if ($job->department)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                    {{ $job->department->name }}
                                                </span>
                                            @endif
                                            @if ($job->site)
                                                <span>·</span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    {{ $job->site->name }}
                                                </span>
                                            @endif
                                            <span>·</span>
                                            <span>{{ $job->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                                {{ $job->level === \App\Enums\JobLevel::Staff ? 'Staff' : 'Non-Staff' }}
                                            </span>
                                            @if ($job->closed_at)
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">
                                                    Closes {{ $job->closed_at->format('d M Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- CTA --}}
                                    <div class="shrink-0">
                                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-bold bg-brand-500 text-white group-hover:bg-brand-400 transition-colors">
                                            View Details
                                            <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <aside class="w-full lg:w-72 shrink-0 space-y-6">

                    {{-- Why Join RJS --}}
                    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-6">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-5">Why Join RJS?</h3>
                        <ul class="space-y-4">
                            @foreach ([
                                ['icon' => '🛡️', 'title' => 'Safety First', 'desc' => '5M+ hours without Lost Time Injury'],
                                ['icon' => '🚀', 'title' => 'Career Growth', 'desc' => 'Continuous learning and development programs'],
                                ['icon' => '🌍', 'title' => 'Impactful Work', 'desc' => 'Contribute to Eastern Indonesia\'s growth'],
                                ['icon' => '🤝', 'title' => 'Strong Community', 'desc' => 'A team of 1,671+ dedicated professionals'],
                            ] as $perk)
                                <li class="flex items-start gap-3">
                                    <span class="text-xl shrink-0">{{ $perk['icon'] }}</span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $perk['title'] }}</p>
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $perk['desc'] }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Latest News --}}
                    @if ($latestNews->isNotEmpty())
                        <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-6">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-5">Company News</h3>
                            <div class="space-y-4">
                                @foreach ($latestNews as $news)
                                    <a href="{{ route('articles.show', $news->slug) }}" class="flex gap-3 group items-start">
                                        <div class="w-14 h-12 rounded-lg overflow-hidden shrink-0 bg-slate-100 dark:bg-slate-800">
                                            @if ($news->featuredImage)
                                                <img src="{{ Storage::url($news->featuredImage->path) }}" alt="{{ $news->title }}"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 group-hover:text-brand-500 transition-colors line-clamp-2 leading-snug">
                                                {{ $news->title }}
                                            </p>
                                            <p class="text-xs text-slate-400 mt-1">{{ $news->published_at->format('d M Y') }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <a href="{{ route('articles.index') }}"
                                class="mt-5 flex items-center gap-1 text-sm font-semibold text-brand-500 hover:text-brand-600 transition-colors">
                                All news
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    @endif

                    {{-- Login CTA --}}
                    @guest
                        <div class="rounded-2xl bg-brand-500 p-6 text-center">
                            <h3 class="text-base font-bold text-white mb-2">Ready to Apply?</h3>
                            <p class="text-sm text-brand-100 mb-4">Create your digital profile and apply for positions.</p>
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center justify-center w-full px-4 py-2.5 text-sm font-bold text-brand-600 bg-white rounded-xl hover:bg-brand-50 transition-colors">
                                Login / Register
                            </a>
                        </div>
                    @endguest
                </aside>

            </div>
        </div>
    </div>

</div>
