<div>

    {{-- Page Hero --}}
    <div class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 py-16 transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm text-slate-400">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-500 transition-colors">Home</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-brand-500 font-medium">News</li>
                </ol>
            </nav>
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-0.5 bg-brand-500"></div>
                        <span class="text-sm font-bold tracking-widest uppercase text-brand-500">Latest Updates</span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white sm:text-4xl">Company News</h1>
                    <p class="mt-3 text-slate-600 dark:text-slate-400">Achievements, operations, and stories from PT. Roda Jaya Sakti.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-10">

                {{-- Main Content --}}
                <div class="flex-1 min-w-0">

                    {{-- Hero Article --}}
                    @if ($heroArticle && !$search && !$category)
                        <a href="{{ route('articles.show', $heroArticle->slug) }}"
                            class="group block mb-10 rounded-3xl overflow-hidden bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-300">
                            <div class="flex flex-col md:flex-row">
                                <div class="md:w-1/2 aspect-video md:aspect-auto overflow-hidden bg-slate-200 dark:bg-slate-700 shrink-0">
                                    @if ($heroArticle->featuredImage)
                                        <img src="{{ Storage::url($heroArticle->featuredImage->path) }}"
                                            alt="{{ $heroArticle->title }}"
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                    @elseif ($heroArticle->image_path)
                                        <img src="{{ Storage::url($heroArticle->image_path) }}"
                                            alt="{{ $heroArticle->title }}"
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                    @else
                                        <div class="w-full h-full min-h-55 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5L18.5 7H20a2 2 0 012 2v10a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-col justify-center p-8 md:p-10">
                                    <div class="flex items-center gap-3 mb-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-brand-500/10 text-brand-600 dark:text-brand-400 uppercase tracking-wider">
                                            {{ $heroArticle->category ?? 'News' }}
                                        </span>
                                        <span class="text-xs text-slate-400">Latest</span>
                                    </div>
                                    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white group-hover:text-brand-500 transition-colors leading-tight mb-4">
                                        {{ $heroArticle->title }}
                                    </h2>
                                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-6 line-clamp-3">
                                        {{ Str::limit(strip_tags($heroArticle->content), 200) }}
                                    </p>
                                    <div class="flex items-center gap-4 text-sm text-slate-400">
                                        @if ($heroArticle->author)
                                            <span class="font-medium text-slate-600 dark:text-slate-300">{{ $heroArticle->author->name }}</span>
                                            <span>·</span>
                                        @endif
                                        <span>{{ $heroArticle->published_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endif

                    {{-- Search + Filter --}}
                    <div class="flex flex-col sm:flex-row gap-3 mb-8">
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search news..."
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
                        </div>
                        {{-- Category Dropdown --}}
                        <div x-data="{
                            open: false,
                            options: { '': 'All Categories', ...{{ Js::from(collect($categories)->mapWithKeys(fn ($c) => [$c => $c])) }} },
                            get label() { return this.options[$wire.category] ?? 'All Categories'; },
                            select(val) { $wire.set('category', val); this.open = false; }
                        }" @click.away="open = false" @keydown.escape.window="open = false" class="relative min-w-40">
                            <button @click="open = !open" type="button"
                                class="flex items-center justify-between w-full px-4 py-2.5 rounded-xl border text-sm transition-all duration-200 cursor-pointer"
                                :class="open ? 'ring-2 ring-brand-500/50 border-brand-300 dark:border-brand-600 bg-white dark:bg-slate-900' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 hover:border-slate-300 dark:hover:border-slate-600'">
                                <span class="truncate" :class="$wire.category ? 'text-slate-900 dark:text-white font-medium' : 'text-slate-500 dark:text-slate-400'" x-text="label"></span>
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
                                class="absolute z-50 mt-2 w-full min-w-max rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50 overflow-auto max-h-60 py-1">
                                <button type="button" @click="select('')"
                                    class="w-full text-left px-4 py-2 text-sm transition-colors flex items-center justify-between gap-3"
                                    :class="!$wire.category ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                    <span>All Categories</span>
                                    <svg x-show="!$wire.category" class="w-4 h-4 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                @foreach ($categories as $cat)
                                    <button type="button" @click="select('{{ $cat }}')"
                                        class="w-full text-left px-4 py-2 text-sm transition-colors flex items-center justify-between gap-3"
                                        :class="$wire.category == '{{ $cat }}' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                        <span>{{ $cat }}</span>
                                        <svg x-show="$wire.category == '{{ $cat }}'" class="w-4 h-4 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Articles Grid --}}
                    @if ($articles->isEmpty())
                        <div class="flex flex-col items-center justify-center py-20 text-center">
                            <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5L18.5 7H20a2 2 0 012 2v10a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300 mb-1">No articles found</h3>
                            <p class="text-slate-400 text-sm">Try adjusting your search or filter.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach ($articles as $article)
                                <a href="{{ route('articles.show', $article->slug) }}" wire:key="{{ $article->id }}"
                                    class="group flex flex-col rounded-2xl overflow-hidden bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                                    <div class="aspect-video overflow-hidden bg-slate-200 dark:bg-slate-700 shrink-0">
                                        @if ($article->featuredImage)
                                            <img src="{{ Storage::url($article->featuredImage->path) }}"
                                                alt="{{ $article->title }}"
                                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        @elseif ($article->image_path)
                                            <img src="{{ Storage::url($article->image_path) }}"
                                                alt="{{ $article->title }}"
                                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5L18.5 7H20a2 2 0 012 2v10a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col flex-1 p-5">
                                        @if ($article->category)
                                            <span class="inline-block mb-2 text-xs font-bold uppercase tracking-wider text-brand-500">{{ $article->category }}</span>
                                        @endif
                                        <h3 class="font-bold text-slate-900 dark:text-white group-hover:text-brand-500 transition-colors leading-snug mb-2 line-clamp-2">
                                            {{ $article->title }}
                                        </h3>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-4 line-clamp-2 flex-1">
                                            {{ Str::limit(strip_tags($article->content), 120) }}
                                        </p>
                                        <div class="flex items-center justify-between text-xs text-slate-400 pt-4 border-t border-slate-100 dark:border-slate-800">
                                            <span>{{ $article->published_at->format('d M Y') }}</span>
                                            <span class="inline-flex items-center gap-1 text-brand-500 font-semibold group-hover:gap-2 transition-all">
                                                Read more
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if ($articles->hasPages())
                            <div class="mt-10">
                                {{ $articles->links() }}
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Sidebar --}}
                <aside class="w-full lg:w-80 shrink-0 space-y-6">

                    {{-- Company Quick Facts --}}
                    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-6">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-5">About RJS</h3>
                        <div class="space-y-4">
                            @foreach ([
                                ['label' => 'Founded', 'value' => 'August 2010'],
                                ['label' => 'Headquarters', 'value' => 'Makassar, South Sulawesi'],
                                ['label' => 'Equipment Fleet', 'value' => '641+ Units'],
                                ['label' => 'Workforce', 'value' => '1,671 Professionals'],
                                ['label' => 'LTI-Free Hours', 'value' => '5 Million+'],
                            ] as $fact)
                                <div class="flex justify-between items-center text-sm border-b border-slate-50 dark:border-slate-800 pb-3 last:border-0 last:pb-0">
                                    <span class="text-slate-500 dark:text-slate-400">{{ $fact['label'] }}</span>
                                    <span class="font-semibold text-slate-900 dark:text-white text-right">{{ $fact['value'] }}</span>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('about') }}"
                            class="mt-5 flex items-center gap-2 text-sm font-semibold text-brand-500 hover:text-brand-600 transition-colors">
                            Learn more about us
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                    {{-- Career CTA --}}
                    <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/20 rounded-full blur-2xl -translate-y-8 translate-x-8"></div>
                        <div class="relative z-10">
                            <span class="text-xs font-bold uppercase tracking-wider text-brand-400">Join Our Team</span>
                            <h3 class="text-lg font-bold text-white mt-2 mb-3">Ready to grow your career?</h3>
                            <p class="text-sm text-slate-400 mb-5">Discover open positions and become part of the RJS family.</p>
                            <a href="{{ route('careers.index') }}"
                                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-slate-900 bg-brand-500 rounded-xl hover:bg-brand-400 transition-colors">
                                View Openings
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- Certifications Badge --}}
                    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-6">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">Certified Standards</h3>
                        <div class="flex gap-3 flex-wrap">
                            @foreach (['ISO 9001:2015', 'ISO 14001:2015', 'ISO 45001:2018'] as $cert)
                                <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                                    {{ $cert }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                </aside>
            </div>
        </div>
    </div>

</div>
