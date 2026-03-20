<div class="flex flex-col gap-8">
    {{-- Modern Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-linear-to-br from-brand-50 via-blue-50 to-emerald-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 border-l-4 border-brand-500 px-6 py-8 text-zinc-900 dark:text-white shadow-xl sm:px-8">
        <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-brand-500/10 blur-2xl"></div>
        <div class="absolute -bottom-4 -left-4 h-32 w-32 rounded-full bg-brand-500/5 dark:bg-slate-500/10 blur-2xl"></div>
        <div class="relative">
            <h1 class="text-2xl font-bold sm:text-3xl">{{ __('Job Openings') }} 🎯</h1>
            <p class="mt-2 text-zinc-600 dark:text-slate-300">{{ __('Browse available positions and apply') }}</p>
        </div>
    </div>



    {{-- Filters --}}
    <div class="theme-surface-soft flex flex-col gap-4 rounded-xl border p-4 backdrop-blur-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <flux:field class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search job title...') }}" icon="magnifying-glass" />
            </flux:field>

            <div class="min-w-40">
                <x-custom-select
                    wire:model.live="department_filter"
                    placeholder="{{ __('All Departments') }}"
                    :options="['' => __('All Departments')] + $departments->pluck('name', 'id')->toArray()"
                />
            </div>

            <div class="min-w-36">
                <x-custom-select
                    wire:model.live="site_filter"
                    placeholder="{{ __('All Sites') }}"
                    :options="['' => __('All Sites')] + $sites->pluck('name', 'id')->toArray()"
                />
            </div>

            <div class="min-w-32">
                <x-custom-select
                    wire:model.live="level_filter"
                    placeholder="{{ __('All Levels') }}"
                    :options="['' => __('All Levels'), 'staff' => 'Staff', 'non_staff' => 'Non-Staff']"
                />
            </div>
        </div>

        <div class="flex items-center gap-2">
            <flux:checkbox wire:model.live="show_applied_only" id="show_applied_only" />
            <label for="show_applied_only" class="cursor-pointer text-sm text-zinc-600 dark:text-zinc-400">
                {{ __('Show Applied Jobs Only') }}
            </label>
        </div>
    </div>

    {{-- Job Listings --}}
    @if ($jobs->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-zinc-200 bg-white/50 p-16 dark:border-zinc-700 dark:bg-zinc-900/50">
            <flux:icon.briefcase class="mb-6 h-16 w-16 text-zinc-300 dark:text-zinc-600" />
            <flux:heading size="lg" class="mb-2">{{ __('No Jobs Found') }}</flux:heading>
            <flux:text class="max-w-md text-center mb-6">{{ __('No open positions match your search. Try adjusting your filters or set up a job alert.') }}</flux:text>
            <flux:button icon="bell" class="bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700">
                {{ __('Set Up Job Alert') }}
            </flux:button>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            @foreach ($jobs as $job)
                <div wire:key="{{ $job->id }}"
                    class="theme-surface group relative overflow-hidden rounded-xl border backdrop-blur-sm hover:shadow-lg hover:shadow-brand-500/5">

                    {{-- Top accent bar --}}
                    <div class="h-1 bg-linear-to-r from-brand-500 to-emerald-500 opacity-60 group-hover:opacity-100 transition-opacity"></div>

                    <div class="p-5">
                        <div class="flex flex-col gap-3">
                            {{-- Title & badges --}}
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-zinc-900 dark:text-white group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors truncate">
                                        {{ $job->title }}
                                    </h3>
                                    <div class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        @if ($job->department)
                                            <span class="flex items-center gap-1">
                                                <flux:icon.building-office class="size-3.5" />
                                                {{ $job->department->name }}
                                            </span>
                                        @endif
                                        @if ($job->site)
                                            <span class="flex items-center gap-1">
                                                <flux:icon.map-pin class="size-3.5" />
                                                {{ $job->site->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Level badge --}}
                                <flux:badge variant="outline" size="sm" class="shrink-0">{{ $job->level->name }}</flux:badge>
                            </div>

                            {{-- Meta row --}}
                            <div class="flex items-center justify-between gap-2 text-xs">
                                <div class="flex items-center gap-3 text-zinc-400 dark:text-zinc-500">
                                    @if ($job->closed_at)
                                        <span class="flex items-center gap-1 text-amber-500">
                                            <flux:icon.clock class="size-3.5" />
                                            {{ __('Closes') }} {{ $job->closed_at->format('d M Y') }}
                                        </span>
                                    @endif
                                    <span>{{ $job->created_at->diffForHumans() }}</span>
                                </div>

                                {{-- Action --}}
                                @if (in_array($job->id, $appliedJobIds))
                                    <div class="flex items-center gap-2">
                                        <flux:badge color="green" icon="check" size="sm">{{ __('Applied') }}</flux:badge>
                                        <flux:button wire:click="openTracking({{ $job->id }})" size="xs" variant="ghost" icon="chart-bar">
                                            {{ __('Track') }}
                                        </flux:button>
                                    </div>
                                @else
                                    <flux:button wire:click="apply({{ $job->id }})" variant="primary" size="sm"
                                        wire:loading.attr="disabled" wire:target="apply({{ $job->id }})"
                                        class="bg-linear-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700">
                                        <span wire:loading.remove wire:target="apply({{ $job->id }})">{{ __('Apply Now') }}</span>
                                        <span wire:loading wire:target="apply({{ $job->id }})">{{ __('Applying...') }}</span>
                                    </flux:button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div>{{ $jobs->links() }}</div>
    @endif

    {{-- Tracking Modal --}}
    <flux:modal wire:model="showTrackingModal" class="w-full max-w-lg">
        @if ($trackingApplication)
            @php
                $stages = \App\Enums\RecruitmentStage::pipelineStages();
                $stages = array_values($stages);
                $currentStage = $trackingApplication->recruitment_stage;
                $isRejected = $currentStage === \App\Enums\RecruitmentStage::REJECTED;
                $currentIndex = array_search($currentStage, $stages);
                $logMap = $trackingApplication->stageLogs->keyBy(fn($l) => $l->stage instanceof \App\Enums\RecruitmentStage ? $l->stage->value : $l->stage);
            @endphp
            <div class="space-y-5">
                <div class="text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-brand-100 dark:bg-brand-900/30">
                        <flux:icon.chart-bar class="size-6 text-brand-600 dark:text-brand-400" />
                    </div>
                    <flux:heading size="lg">{{ __('Application Progress') }}</flux:heading>
                    <flux:text class="mt-1 text-sm text-zinc-500">{{ $trackingApplication->job->title }}</flux:text>
                </div>

                <div class="flex flex-col">
                    @foreach ($stages as $i => $stage)
                        @php
                            $log = $logMap->get($stage->value);
                            $isPassed = $log && $log->decision === 'passed';
                            $isRejectedHere = $log && $log->decision === 'rejected';
                            $isCurrent = !$log && $currentIndex === $i;
                            $isUpcoming = !$log && $currentIndex !== false && $i > $currentIndex && !$isRejected;
                        @endphp
                        <div class="flex gap-3">
                            {{-- Connector + circle --}}
                            <div class="flex flex-col items-center">
                                @if ($i > 0)
                                    <div class="w-px h-3 flex-none {{ $isPassed ? 'bg-green-400' : ($isRejectedHere ? 'bg-red-400' : 'bg-zinc-200 dark:bg-zinc-700') }}"></div>
                                @else
                                    <div class="h-3"></div>
                                @endif
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-semibold transition-all
                                    {{ $isPassed ? 'bg-green-500 text-white shadow-md shadow-green-500/20' : ($isRejectedHere ? 'bg-red-500 text-white shadow-md shadow-red-500/20' : ($isCurrent ? 'bg-brand-500 text-white ring-4 ring-brand-500/20 shadow-md shadow-brand-500/20' : 'bg-zinc-200 text-zinc-400 dark:bg-zinc-700')) }}">
                                    @if ($isPassed)
                                        <flux:icon.check class="size-4" />
                                    @elseif ($isRejectedHere)
                                        <flux:icon.x-mark class="size-4" />
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </div>
                                @if (!$loop->last)
                                    <div class="w-px flex-1 min-h-3 {{ $isPassed ? 'bg-green-400' : 'bg-zinc-200 dark:bg-zinc-700' }}"></div>
                                @endif
                            </div>

                            {{-- Stage label + log detail --}}
                            <div class="pb-3 pt-2.5 flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium
                                        {{ $isPassed ? 'text-green-700 dark:text-green-400' : ($isRejectedHere ? 'text-red-600 dark:text-red-400' : ($isCurrent ? 'text-brand-600 dark:text-brand-400' : 'text-zinc-400')) }}">
                                        {{ $stage->label() }}
                                    </span>
                                    @if ($isCurrent)
                                        <flux:badge color="blue" size="sm">{{ __('Current') }}</flux:badge>
                                    @endif
                                </div>
                                @if ($log)
                                    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        <span>{{ $log->created_at->format('d M Y') }}</span>
                                        @if ($log->notes)
                                            <p class="mt-0.5">{{ $log->notes }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($isRejected)
                    <flux:callout variant="danger" icon="x-circle">
                        <flux:callout.heading>{{ __('Application Not Proceeding') }}</flux:callout.heading>
                        <flux:callout.text>{{ __('We appreciate your interest. Please apply for other open positions.') }}</flux:callout.text>
                    </flux:callout>
                @endif

                <div class="flex justify-end">
                    <flux:button type="button" variant="ghost" wire:click="$set('showTrackingModal', false)">
                        {{ __('Close') }}
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
