<div class="flex flex-col gap-8">
    {{-- Modern Header --}}
    <div class="relative overflow-hidden rounded-2xl border-l-4 border-brand-500 bg-linear-to-br from-brand-50 via-blue-50 to-emerald-50 px-6 py-8 text-zinc-900 shadow-xl dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 dark:text-white sm:px-8">
        <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-brand-500/10 blur-2xl"></div>
        <div class="absolute -bottom-4 -left-4 h-32 w-32 rounded-full bg-brand-500/5 blur-2xl dark:bg-slate-500/10"></div>
        <div class="relative">
            <h1 class="text-2xl font-bold sm:text-3xl">{{ __('My Applications') }} 📋</h1>
            <p class="mt-2 text-zinc-600 dark:text-slate-300">{{ __('Track the status of your job applications') }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="theme-surface-soft flex flex-col gap-3 sm:flex-row rounded-xl border p-4 backdrop-blur-sm">
        <flux:field class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search job title...') }}" icon="magnifying-glass" />
        </flux:field>
        <div class="min-w-44">
            <x-custom-select
                wire:model.live="statusFilter"
                placeholder="{{ __('All Statuses') }}"
                :options="['' => __('All Statuses')] + collect($statuses)->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()"
            />
        </div>
    </div>

    @if ($applications->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-zinc-200 bg-white/50 p-16 dark:border-zinc-700 dark:bg-zinc-900/50">
            <flux:icon.document-text class="mb-6 h-16 w-16 text-zinc-300 dark:text-zinc-600" />
            <flux:heading size="lg" class="mb-2">{{ __('No Applications Yet') }}</flux:heading>
            <flux:text class="mb-6 max-w-md text-center">{{ __('You have not applied to any positions yet.') }}</flux:text>
            <flux:button variant="primary" href="{{ route('candidate.portal') }}" wire:navigate
                class="bg-linear-to-r from-brand-500 to-brand-600">
                {{ __('Browse Jobs') }}
            </flux:button>
        </div>
    @else
        <div class="flex flex-col gap-5">
            @foreach ($applications as $application)
                <div wire:key="{{ $application->id }}"
                    class="theme-surface group overflow-hidden rounded-xl border backdrop-blur-sm shadow-sm hover:shadow-md">

                    {{-- Header --}}
                    <div class="flex flex-col gap-1 p-5 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-100 dark:bg-brand-900/30">
                                <flux:icon.briefcase class="size-5 text-brand-600 dark:text-brand-400" />
                            </div>
                            <div>
                                <flux:heading size="md" class="group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">{{ $application->job->title }}</flux:heading>
                                <flux:text class="text-xs text-zinc-500 mt-0.5">
                                    @if ($application->job->department){{ $application->job->department->name }}@endif
                                    @if ($application->job->site) · {{ $application->job->site->name }}@endif
                                </flux:text>
                            </div>
                        </div>
                        <flux:text class="text-xs text-zinc-400 sm:text-right shrink-0">
                            {{ __('Applied') }} {{ $application->created_at->format('d M Y') }}
                        </flux:text>
                    </div>

                    {{-- Progress Pipeline --}}
                    <div class="border-t border-zinc-100 bg-zinc-50/80 px-5 py-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                        @php
                            $stages = \App\Enums\RecruitmentStage::pipelineStages();
                            $stages = array_values($stages);
                            $currentStage = $application->recruitment_stage;
                            $isRejected = $currentStage === \App\Enums\RecruitmentStage::REJECTED;
                            $currentIndex = array_search($currentStage, $stages);
                            $logMap = $application->stageLogs->keyBy(fn($l) => $l->stage instanceof \App\Enums\RecruitmentStage ? $l->stage->value : $l->stage);
                        @endphp

                        {{-- Modern stage stepper --}}
                        @php
                            $stageItems = collect($stages)->values()->map(function ($stage, $index) use ($logMap, $currentIndex, $isRejected) {
                                $log = $logMap->get($stage->value);
                                $isPassed = $log && $log->decision === 'passed';
                                $isRejectedHere = $log && $log->decision === 'rejected';
                                $isCurrent = !$log && $currentIndex === $index;

                                $state = 'pending';

                                if ($isPassed) {
                                    $state = 'passed';
                                } elseif ($isRejectedHere) {
                                    $state = 'rejected';
                                } elseif ($isCurrent) {
                                    $state = 'current';
                                } elseif ($isRejected && $currentIndex !== false && $index < $currentIndex) {
                                    $state = 'passed';
                                }

                                return [
                                    'state' => $state,
                                    'stage' => $stage,
                                    'index' => $index,
                                ];
                            })->all();
                        @endphp

                        @php
                            $currentStageItem = collect($stageItems)->first(fn($item) => in_array($item['state'], ['current', 'rejected'], true));

                            if (!$currentStageItem) {
                                $currentStageItem = collect($stageItems)->last(fn($item) => $item['state'] === 'passed');
                            }

                            if (!$currentStageItem) {
                                $currentStageItem = $stageItems[0] ?? null;
                            }

                            $activeIndex = $currentStageItem['index'] ?? 0;
                            $stageTotal = max(count($stageItems), 1);
                            $progressPercent = (int) round((($activeIndex + 1) / $stageTotal) * 100);
                            $currentStageLabel = $currentStageItem['stage']->label() ?? __('Application Submitted');
                        @endphp

                        <div class="app-stage-meta mb-3 flex items-center justify-between gap-3 rounded-lg border px-3 py-2">
                            <div class="min-w-0">
                                <p class="text-[11px] uppercase tracking-[0.14em] app-stage-meta-kicker">{{ __('Current Stage') }}</p>
                                <p class="truncate text-sm font-semibold app-stage-meta-title">{{ $currentStageLabel }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-[11px] app-stage-meta-caption">{{ __('Progress') }}</p>
                                <p class="text-sm font-semibold app-stage-meta-title">{{ $activeIndex + 1 }}/{{ $stageTotal }} · {{ $progressPercent }}%</p>
                            </div>
                        </div>

                        <div class="app-stage-scroll">
                            <div class="inline-flex min-w-max items-start gap-2 sm:gap-3">
                                @foreach ($stageItems as $i => $item)
                                    @php
                                        $state = $item['state'];
                                        $segmentActive = $i < $activeIndex;
                                        $nodeClass = match ($state) {
                                            'passed' => 'app-stage-node-passed',
                                            'current' => 'app-stage-node-current',
                                            'rejected' => 'app-stage-node-rejected',
                                            default => 'app-stage-node-pending',
                                        };
                                        $labelClass = match ($state) {
                                            'passed' => 'app-stage-label-passed',
                                            'current' => 'app-stage-label-current',
                                            'rejected' => 'app-stage-label-rejected',
                                            default => 'app-stage-label-pending',
                                        };
                                    @endphp

                                    <div class="flex items-start gap-2 sm:gap-3">
                                        <div class="flex shrink-0 flex-col items-center gap-1.5">
                                            <div class="app-stage-node {{ $nodeClass }}">
                                                @if ($state === 'passed')
                                                    <flux:icon.check class="size-3.5" />
                                                @elseif ($state === 'rejected')
                                                    <flux:icon.x-mark class="size-3.5" />
                                                @else
                                                    {{ $item['index'] + 1 }}
                                                @endif
                                            </div>
                                            <span class="app-stage-label {{ $labelClass }} {{ in_array($state, ['current', 'rejected'], true) ? '' : 'hidden sm:block' }}">{{ $item['stage']->label() }}</span>
                                        </div>

                                        @if ($i < count($stageItems) - 1)
                                            <div class="app-stage-segment mt-3 {{ $segmentActive ? 'app-stage-segment-active' : '' }}"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Offering Letter Response Section --}}
                        @if ($currentStage === \App\Enums\RecruitmentStage::OFFERING && $application->offeringLetter)
                            <div class="mt-4 rounded-xl border-2 border-brand-200 bg-brand-50/50 p-5 dark:border-brand-900/30 dark:bg-brand-900/10">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 dark:bg-brand-900/50">
                                        <flux:icon.document-text class="size-5 text-brand-600 dark:text-brand-400" />
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-zinc-900 dark:text-white">{{ __('Surat Penawaran Kerja (Offering Letter)') }}</h4>
                                        <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Silakan tinjau dokumen penawaran di bawah ini.') }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    @if($application->offeringLetter->file_path)
                                        <a href="{{ Storage::url($application->offeringLetter->file_path) }}" target="_blank"
                                            class="inline-flex items-center gap-2 rounded-lg bg-white border px-4 py-2 text-sm font-semibold text-zinc-700 shadow-sm transition-all hover:bg-zinc-50 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-700">
                                            <flux:icon.arrow-down-tray class="size-4" />
                                            {{ __('Unduh Offering Letter (PDF)') }}
                                        </a>
                                    @else
                                        <span class="text-sm text-zinc-500 italic">{{ __('Dokumen sedang disiapkan...') }}</span>
                                    @endif

                                    <div class="flex items-center gap-2">
                                        <flux:modal.trigger name="reject-offer-{{ $application->id }}">
                                            <flux:button variant="ghost" size="sm" class="text-red-600 hover:text-red-700 hover:bg-red-50">
                                                {{ __('Tolak') }}
                                            </flux:button>
                                        </flux:modal.trigger>

                                        <flux:modal.trigger name="accept-offer-{{ $application->id }}">
                                            <flux:button variant="primary" size="sm" class="bg-emerald-600 hover:bg-emerald-700">
                                                {{ __('Terima Penawaran') }}
                                            </flux:button>
                                        </flux:modal.trigger>
                                    </div>
                                </div>

                                {{-- Modals for Confirmation --}}
                                <flux:modal name="accept-offer-{{ $application->id }}" class="max-w-md">
                                    <div class="space-y-4">
                                        <flux:heading size="lg">{{ __('Terima Penawaran?') }}</flux:heading>
                                        <flux:text>{{ __('Dengan mengklik Ya, Anda menyatakan menyetujui penawaran pekerjaan ini dan akan lanjut ke tahap Psychotest.') }}</flux:text>
                                        <div class="flex justify-end gap-3">
                                            <flux:modal.close>
                                                <flux:button variant="ghost">{{ __('Batal') }}</flux:button>
                                            </flux:modal.close>
                                            <flux:button variant="primary" class="bg-emerald-600" wire:click="acceptOffer({{ $application->id }})" wire:target="acceptOffer({{ $application->id }})" x-on:click="$dispatch('modal-close')">
                                                {{ __('Ya, Saya Terima') }}
                                            </flux:button>
                                        </div>
                                    </div>
                                </flux:modal>

                                <flux:modal name="reject-offer-{{ $application->id }}" class="max-w-md">
                                    <div class="space-y-4">
                                        <flux:heading size="lg" class="text-red-600">{{ __('Tolak Penawaran?') }}</flux:heading>
                                        <flux:text>{{ __('Apakah Anda yakin ingin menolak penawaran pekerjaan ini? Tindakan ini tidak dapat dibatalkan.') }}</flux:text>
                                        <div class="flex justify-end gap-3">
                                            <flux:modal.close>
                                                <flux:button variant="ghost">{{ __('Batal') }}</flux:button>
                                            </flux:modal.close>
                                            <flux:button variant="danger" wire:click="rejectOffer({{ $application->id }})" wire:target="rejectOffer({{ $application->id }})" x-on:click="$dispatch('modal-close')">
                                                {{ __('Ya, Tolak') }}
                                            </flux:button>
                                        </div>
                                    </div>
                                </flux:modal>
                            </div>
                        @endif

                        {{-- Rejection info --}}
                        @if ($isRejected && $application->stageLogs->isNotEmpty())
                            <div class="mt-4 space-y-2">
                                <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">{{ __('Application History') }}</p>
                                @foreach ($application->stageLogs->sortBy('created_at') as $log)
                                    <div class="rounded-lg px-3 py-2 text-xs {{ $log->decision === 'passed' ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="font-semibold {{ $log->decision === 'passed' ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                                                {{ $log->stage->label() }} — {{ $log->decision === 'passed' ? __('Passed') : __('Not Selected') }}
                                            </span>
                                            <span class="shrink-0 text-zinc-400">{{ $log->created_at->format('d M Y') }}</span>
                                        </div>
                                        @if ($log->notes)
                                            <p class="mt-1 text-zinc-600 dark:text-zinc-400">{{ $log->notes }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @elseif ($isRejected)
                            <flux:callout variant="danger" icon="x-circle" class="mt-3">
                                <flux:callout.heading>{{ __('Application Not Proceeding') }}</flux:callout.heading>
                                <flux:callout.text>{{ __('We appreciate your interest. Please apply for other open positions.') }}</flux:callout.text>
                            </flux:callout>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div>{{ $applications->links() }}</div>
    @endif
</div>
