<div>

    <div class="py-10 bg-slate-50 dark:bg-slate-950 min-h-screen transition-colors duration-300">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm text-slate-400">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-500 transition-colors">Home</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li><a href="{{ route('careers.index') }}"
                            class="hover:text-brand-500 transition-colors">Careers</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-slate-600 dark:text-slate-300 font-medium truncate max-w-xs">{{ $job->title }}</li>
                </ol>
            </nav>

            <div class="flex flex-col lg:flex-row gap-10">

                {{-- Main Content --}}
                <div class="flex-1 min-w-0 space-y-6">

                    {{-- Job Header Card --}}
                    <div
                        class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
                        {{-- Featured Image --}}
                        @if ($job->featuredImage)
                            <div class="aspect-21/7 overflow-hidden">
                                <img src="{{ Storage::url($job->featuredImage->path) }}" alt="{{ $job->title }}"
                                    class="w-full h-full object-cover">
                            </div>
                        @endif
                        <div class="p-8 md:p-10">
                            {{-- Badges --}}
                            <div class="flex flex-wrap gap-2 mb-5">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-bold bg-brand-500/10 text-brand-600 dark:text-brand-400 uppercase tracking-wider">
                                    {{ $job->level === \App\Enums\JobLevel::Staff ? 'Staff' : 'Non-Staff' }}
                                </span>
                                @if ($job->department)
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                        {{ $job->department->name }}
                                    </span>
                                @endif
                                @if ($job->site)
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                        📍 {{ $job->site->name }}
                                    </span>
                                @endif
                                @if ($job->closed_at)
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">
                                        ⏰ Closes {{ $job->closed_at->format('d M Y') }}
                                    </span>
                                @endif
                            </div>

                            <h1
                                class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white leading-tight mb-4">
                                {{ $job->title }}
                            </h1>
                            <p class="text-sm text-slate-400">Posted {{ $job->created_at->format('d F Y') }} ·
                                {{ $job->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    {{-- Job Description --}}
                    <div
                        class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 md:p-10 shadow-sm">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-xl bg-brand-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Job Description</h2>
                        </div>
                        <div class="prose prose-slate dark:prose-invert max-w-none
                            prose-p:text-slate-600 dark:prose-p:text-slate-400 prose-p:leading-relaxed
                            prose-headings:text-slate-900 dark:prose-headings:text-white prose-headings:font-bold">
                            {!! nl2br(e($job->description)) !!}
                        </div>
                    </div>

                    {{-- Requirements --}}
                    <div
                        class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 md:p-10 shadow-sm">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-xl bg-brand-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Requirements</h2>
                        </div>
                        <div class="prose prose-slate dark:prose-invert max-w-none
                            prose-p:text-slate-600 dark:prose-p:text-slate-400 prose-p:leading-relaxed
                            prose-headings:text-slate-900 dark:prose-headings:text-white">
                            {!! nl2br(e($job->requirements)) !!}
                        </div>
                    </div>

                    @if (filled($job->benefits))
                        <div
                            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 md:p-10 shadow-sm">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5-1a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Benefits</h2>
                            </div>
                            <div class="prose prose-slate dark:prose-invert max-w-none
                                        prose-p:text-slate-600 dark:prose-p:text-slate-400 prose-p:leading-relaxed
                                        prose-headings:text-slate-900 dark:prose-headings:text-white">
                                {!! nl2br(e($job->benefits)) !!}
                            </div>
                        </div>
                    @endif

                    {{-- Image Gallery --}}
                    @php $galleryImages = $job->images->where('is_featured', false); @endphp
                    @if ($galleryImages->isNotEmpty())
                        <div
                            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 md:p-10 shadow-sm">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Work Environment</h2>
                            <div x-data="{ lightbox: null }" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach ($galleryImages as $img)
                                    <div @click="lightbox = '{{ Storage::url($img->path) }}'"
                                        class="group aspect-video rounded-xl overflow-hidden cursor-pointer bg-slate-100 dark:bg-slate-800">
                                        <img src="{{ Storage::url($img->path) }}" alt="Work environment"
                                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    </div>
                                @endforeach
                                {{-- Lightbox --}}
                                <div x-show="lightbox" x-transition.opacity style="display:none;"
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
                                    @click="lightbox = null" @keydown.escape.window="lightbox = null">
                                    <img :src="lightbox" class="max-w-full max-h-full rounded-xl shadow-2xl object-contain"
                                        @click.stop>
                                    <button @click="lightbox = null"
                                        class="absolute top-4 right-4 text-white bg-white/10 hover:bg-white/20 rounded-full p-2">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Related Jobs --}}
                    @if ($relatedJobs->isNotEmpty())
                        <div
                            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 md:p-10 shadow-sm">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Related Positions</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($relatedJobs as $related)
                                    <a href="{{ route('careers.show', $related) }}"
                                        class="group p-4 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-brand-200 dark:hover:border-brand-800 hover:shadow-md transition-all">
                                        <h4
                                            class="font-bold text-slate-900 dark:text-white group-hover:text-brand-500 transition-colors text-sm">
                                            {{ $related->title }}
                                        </h4>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @if ($related->department)
                                                <span class="text-xs text-slate-400">{{ $related->department->name }}</span>
                                            @endif
                                            @if ($related->site)
                                                <span class="text-slate-300 dark:text-slate-600 text-xs">·</span>
                                                <span class="text-xs text-slate-400">{{ $related->site->name }}</span>
                                            @endif
                                        </div>
                                        <span
                                            class="mt-3 inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500">
                                            {{ $related->level === \App\Enums\JobLevel::Staff ? 'Staff' : 'Non-Staff' }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Sidebar --}}
                <aside class="w-full lg:w-72 shrink-0 space-y-6">

                    {{-- Apply CTA --}}
                    <div
                        class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-6 shadow-sm sticky top-6">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-5">Apply for this Position</h3>

                        @auth
                            @if (auth()->user()->role->isUser())
                                <a href="{{ route('candidate.portal') }}"
                                    class="flex items-center justify-center w-full px-4 py-3 text-sm font-bold text-white rounded-xl bg-brand-500 hover:bg-brand-400 transition-colors mb-3">
                                    Apply via Career Portal
                                    <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                                <p class="text-xs text-slate-400 text-center">You will be directed to the candidate portal to
                                    complete your application.</p>
                            @else
                                <div class="flex flex-col items-center text-center py-4">
                                    <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mb-3" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Applications are for candidates only.
                                    </p>
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                                class="flex items-center justify-center w-full px-4 py-3 text-sm font-bold text-white rounded-xl bg-brand-500 hover:bg-brand-400 transition-colors mb-3">
                                Login to Apply
                            </a>
                            <a href="{{ route('register') }}"
                                class="flex items-center justify-center w-full px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                                Create an Account
                            </a>
                            <p class="text-xs text-slate-400 text-center mt-3">Register as a candidate to apply for
                                positions at PT. Roda Jaya Sakti.</p>
                        @endauth

                        <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 space-y-3">
                            <div class="flex items-center gap-3 text-sm">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span
                                    class="text-slate-500 dark:text-slate-400">{{ $job->department?->name ?? 'All Departments' }}</span>
                            </div>
                            @if ($job->site)
                                <div class="flex items-center gap-3 text-sm">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-slate-500 dark:text-slate-400">{{ $job->site->name }}</span>
                                </div>
                            @endif
                            <div class="flex items-center gap-3 text-sm">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span
                                    class="text-slate-500 dark:text-slate-400">{{ $job->level === \App\Enums\JobLevel::Staff ? 'Staff' : 'Non-Staff' }}</span>
                            </div>
                            @if ($job->closed_at)
                                <div class="flex items-center gap-3 text-sm">
                                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-amber-500 dark:text-amber-400 font-medium">Closes
                                        {{ $job->closed_at->format('d M Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Company Info --}}
                    <div
                        class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-6">
                        <img class="h-8 mb-4 dark:brightness-0 dark:invert"
                            src="{{ asset('rjs-photos/LOGO-RJS-tanpa-work-scoop-300x80.png') }}" alt="RJS Logo">
                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                            PT Roda Jaya Sakti is a leading mining services contractor in Eastern Indonesia, established
                            in 2010 with 641+ equipment units and 1,671+ professionals.
                        </p>
                        <a href="{{ route('about') }}"
                            class="mt-4 flex items-center gap-1 text-sm font-semibold text-brand-500 hover:text-brand-600 transition-colors">
                            Learn more
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                </aside>
            </div>
        </div>
    </div>

</div>