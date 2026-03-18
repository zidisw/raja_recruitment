<div>

    <div class="py-10 bg-slate-50 dark:bg-slate-950 min-h-screen transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm text-slate-400">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-500 transition-colors">Home</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li><a href="{{ route('articles.index') }}" class="hover:text-brand-500 transition-colors">News</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-slate-600 dark:text-slate-300 font-medium truncate max-w-xs">{{ $article->title }}</li>
                </ol>
            </nav>

            <div class="flex flex-col lg:flex-row gap-10">

                {{-- Main Article --}}
                <article class="flex-1 min-w-0">
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">

                        {{-- Featured Image --}}
                        @php
                            $featured = $article->featuredImage ?? null;
                            $coverSrc = $featured ? Storage::url($featured->path) : ($article->image_path ? Storage::url($article->image_path) : null);
                        @endphp
                        @if ($coverSrc)
                            <div class="aspect-16/7 overflow-hidden">
                                <img src="{{ $coverSrc }}" alt="{{ $article->title }}"
                                    class="w-full h-full object-cover">
                            </div>
                        @endif

                        <div class="p-8 md:p-12">
                            {{-- Meta --}}
                            <div class="flex flex-wrap items-center gap-3 mb-6">
                                @if ($article->category)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-brand-500/10 text-brand-600 dark:text-brand-400 uppercase tracking-wider">
                                        {{ $article->category }}
                                    </span>
                                @endif
                                <span class="text-sm text-slate-400">{{ $article->published_at->format('d F Y') }}</span>
                                @if ($article->author)
                                    <span class="text-slate-300 dark:text-slate-600">·</span>
                                    <span class="text-sm text-slate-500 dark:text-slate-400">By <strong class="text-slate-700 dark:text-slate-200">{{ $article->author->name }}</strong></span>
                                @endif
                            </div>

                            {{-- Title --}}
                            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white leading-tight mb-8">
                                {{ $article->title }}
                            </h1>

                            {{-- Content --}}
                            <div class="prose prose-slate dark:prose-invert max-w-none prose-lg
                                prose-headings:font-bold prose-headings:text-slate-900 dark:prose-headings:text-white
                                prose-p:text-slate-600 dark:prose-p:text-slate-400 prose-p:leading-relaxed
                                prose-a:text-brand-500 prose-a:no-underline hover:prose-a:underline
                                prose-strong:text-slate-800 dark:prose-strong:text-slate-200">
                                {!! nl2br(e($article->content)) !!}
                            </div>

                            {{-- Gallery --}}
                            @php
                                $galleryImages = $article->images->where('is_featured', false);
                            @endphp
                            @if ($galleryImages->isNotEmpty())
                                <div class="mt-10 pt-8 border-t border-slate-100 dark:border-slate-800">
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-5">Photo Gallery</h3>
                                    <div x-data="{ lightbox: null }" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                        @foreach ($galleryImages as $img)
                                            <div @click="lightbox = '{{ Storage::url($img->path) }}'"
                                                class="group aspect-square rounded-xl overflow-hidden cursor-pointer bg-slate-100 dark:bg-slate-800">
                                                <img src="{{ Storage::url($img->path) }}" alt="Gallery image"
                                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                            </div>
                                        @endforeach

                                        {{-- Lightbox --}}
                                        <div x-show="lightbox" x-transition.opacity style="display:none;"
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
                                            @click="lightbox = null" @keydown.escape.window="lightbox = null">
                                            <img :src="lightbox" class="max-w-full max-h-full rounded-xl shadow-2xl object-contain" @click.stop>
                                            <button @click="lightbox = null"
                                                class="absolute top-4 right-4 text-white bg-white/10 hover:bg-white/20 rounded-full p-2 transition-colors">
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Share + Back --}}
                            <div class="mt-10 pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <a href="{{ route('articles.index') }}"
                                    class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-brand-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    Back to News
                                </a>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-slate-400">Share:</span>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}"
                                        target="_blank" rel="noopener"
                                        class="p-2 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-brand-500 hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                        </svg>
                                    </a>
                                    <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . request()->url()) }}"
                                        target="_blank" rel="noopener"
                                        class="p-2 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-green-500 hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                {{-- Sidebar --}}
                <aside class="w-full lg:w-80 shrink-0 space-y-6">

                    {{-- Author Card --}}
                    @if ($article->author)
                        <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-6">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">Author</h3>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-brand-500 flex items-center justify-center text-white font-bold text-lg shrink-0">
                                    {{ mb_substr($article->author->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $article->author->name }}</p>
                                    <p class="text-sm text-slate-400">PT. Roda Jaya Sakti</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Related Articles --}}
                    @if ($relatedArticles->isNotEmpty())
                        <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-6">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-5">Related News</h3>
                            <div class="space-y-4">
                                @foreach ($relatedArticles as $related)
                                    <a href="{{ route('articles.show', $related->slug) }}"
                                        class="flex gap-3 group items-start">
                                        <div class="w-16 h-14 rounded-lg overflow-hidden shrink-0 bg-slate-100 dark:bg-slate-800">
                                            @if ($related->featuredImage)
                                                <img src="{{ Storage::url($related->featuredImage->path) }}" alt="{{ $related->title }}"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 group-hover:text-brand-500 transition-colors line-clamp-2 leading-snug">
                                                {{ $related->title }}
                                            </p>
                                            <p class="text-xs text-slate-400 mt-1">{{ $related->published_at->format('d M Y') }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <a href="{{ route('articles.index') }}"
                                class="mt-5 flex items-center gap-1 text-sm font-semibold text-brand-500 hover:text-brand-600 transition-colors">
                                View all news
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    @endif

                    {{-- Latest Jobs --}}
                    @if ($latestJobs->isNotEmpty())
                        <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/20 rounded-full blur-2xl -translate-y-8 translate-x-8"></div>
                            <div class="relative z-10">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-white mb-5">Open Positions</h3>
                                <div class="space-y-3">
                                    @foreach ($latestJobs as $job)
                                        <a href="{{ route('careers.show', $job) }}"
                                            class="block p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 hover:border-brand-500/30 transition-all group">
                                            <p class="text-sm font-semibold text-white group-hover:text-brand-400 transition-colors">{{ $job->title }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                @if ($job->department)
                                                    <span class="text-xs text-slate-400">{{ $job->department->name }}</span>
                                                @endif
                                                @if ($job->site)
                                                    <span class="text-slate-600">·</span>
                                                    <span class="text-xs text-slate-400">{{ $job->site->name }}</span>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                                <a href="{{ route('careers.index') }}"
                                    class="mt-5 inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-slate-900 bg-brand-500 rounded-xl hover:bg-brand-400 transition-colors">
                                    View all openings
                                </a>
                            </div>
                        </div>
                    @endif

                </aside>
            </div>
        </div>
    </div>

</div>
