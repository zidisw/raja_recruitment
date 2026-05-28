<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ $job->title }}</flux:heading>
            <flux:subheading size="lg">{{ __('Applicants') }} · {{ $job->department?->name }}</flux:subheading>
        </div>
        <div class="flex flex-wrap gap-2">
            <flux:button href="{{ route('applications.index') }}" wire:navigate variant="ghost" icon="arrow-left"
                size="sm" class="border! border-zinc-300! bg-zinc-100/70! dark:border-zinc-700! dark:bg-zinc-800/70!">
                {{ __('Back to Jobs') }}
            </flux:button>
            <flux:button wire:click="exportExcel" variant="primary" icon="arrow-down-tray" size="sm"
                class="bg-emerald-600! hover:bg-emerald-700! text-white! dark:bg-emerald-500! dark:hover:bg-emerald-600!">
                {{ __('Export Excel') }}
            </flux:button>
            <flux:button wire:click="openBulkEmail" variant="primary" icon="paper-airplane" size="sm"
                class="bg-brand-600! hover:bg-brand-700! text-white! dark:bg-brand-500! dark:hover:bg-brand-600!">
                {{ __('Bulk Send Email') }}
            </flux:button>
            <flux:button wire:click="openBulkReject" variant="primary" icon="x-mark" size="sm"
                class="bg-rose-600! hover:bg-rose-700! text-white! dark:bg-rose-500! dark:hover:bg-rose-600!">
                {{ __('Bulk Reject') }}
            </flux:button>
        </div>
    </div>

    <flux:separator variant="subtle" />

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>{{ session('success') }}</flux:callout.heading>
        </flux:callout>
    @endif

    {{-- Filters --}}
    <div class="flex flex-col gap-4">
        {{-- Search + Per Page --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <flux:field class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search candidate name or email...') }}" icon="magnifying-glass" />
            </flux:field>
            <div class="flex items-center gap-2">
                <span class="text-sm text-zinc-500">{{ __('Per page:') }}</span>
                <div class="w-20">
                    <x-custom-select wire:model.live="perPage" placeholder="10" :options="['10' => '10', '30' => '30', '50' => '50', '100' => '100']" />
                </div>
            </div>
        </div>

        {{-- Compact Filter Drawer (No Flicker) --}}
        <div x-data="{
            open: false,
            status: @js($statusFilter),
            gender: @js($genderFilter),
            religion: @js($religionFilter),
            degree: @js($degreeFilter),
            documents: @js($documentsFilter),
            hasExperience: @js($hasExperience),
            hasOrganization: @js($hasOrganization),
            get activeCount() {
                return this.status.length + this.gender.length + this.religion.length + this.degree.length + this.documents.length + (this.hasExperience ? 1 : 0) + (this.hasOrganization ? 1 : 0);
            },
            applyAll() {
                $wire.set('statusFilter', this.status);
                $wire.set('genderFilter', this.gender);
                $wire.set('religionFilter', this.religion);
                $wire.set('degreeFilter', this.degree);
                $wire.set('documentsFilter', this.documents);
                $wire.set('hasExperience', this.hasExperience);
                $wire.set('hasOrganization', this.hasOrganization);
                this.open = false;
            },
            resetLocal() {
                this.status = [];
                this.gender = [];
                this.religion = [];
                this.degree = [];
                this.documents = [];
                this.hasExperience = '';
                this.hasOrganization = '';
                $wire.call('resetFilters');
            }
        }" wire:ignore class="relative">
            <button @click="open = !open" type="button" class="modern-filter-btn">
                <flux:icon.funnel class="size-4 text-zinc-400" />
                <span>{{ __('Filters') }}</span>
                <span x-show="activeCount" x-text="activeCount"
                    class="inline-flex min-w-5 items-center justify-center rounded-full bg-brand-500 px-1.5 py-0.5 text-[11px] font-semibold text-white"></span>
                <flux:icon.chevron-down class="size-3.5 text-zinc-400 transition-transform"
                    ::class="open ? 'rotate-180' : ''" />
            </button>

            <div x-show="open" x-cloak @click.outside="open = false"
                class="modern-filter-panel mt-2 w-full max-w-none p-4 sm:p-5">
                <div class="mb-3 flex items-center justify-between gap-2">
                    <flux:heading size="sm">{{ __('Advanced Filters') }}</flux:heading>
                    <div class="flex items-center gap-2">
                        <flux:button @click="applyAll()" type="button" size="sm" variant="primary" icon="funnel">
                            {{ __('Apply') }}
                        </flux:button>
                        <flux:button @click="resetLocal()" type="button" size="sm" variant="ghost" icon="x-mark">
                            {{ __('Reset') }}
                        </flux:button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('Status') }}</p>
                        @foreach ($statuses as $status)
                            <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                                <input type="checkbox" x-model="status" value="{{ $status->value }}"
                                    class="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-500/30 dark:border-zinc-600 dark:bg-zinc-800" />
                                {{ $status->label() }}
                            </label>
                        @endforeach
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('Gender') }}
                            </p>
                            @foreach ([['male', __('Male')], ['female', __('Female')]] as [$val, $label])
                                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                                    <input type="checkbox" x-model="gender" value="{{ $val }}"
                                        class="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-500/30 dark:border-zinc-600 dark:bg-zinc-800" />
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>

                        <div class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">
                                {{ __('Experience') }}
                            </p>
                            <select x-model="hasExperience"
                                class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                                <option value="">{{ __('All Experience') }}</option>
                                <option value="yes">{{ __('Has Experience') }}</option>
                                <option value="no">{{ __('No Experience') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('Religion') }}
                            </p>
                            @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $rel)
                                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                                    <input type="checkbox" x-model="religion" value="{{ $rel }}"
                                        class="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-500/30 dark:border-zinc-600 dark:bg-zinc-800" />
                                    {{ $rel }}
                                </label>
                            @endforeach
                        </div>

                        <div class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">
                                {{ __('Organization') }}
                            </p>
                            <select x-model="hasOrganization"
                                class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                                <option value="">{{ __('All Organization') }}</option>
                                <option value="yes">{{ __('Has Organization') }}</option>
                                <option value="no">{{ __('No Organization') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('Degree') }}
                            </p>
                            @foreach (['SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'] as $deg)
                                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                                    <input type="checkbox" x-model="degree" value="{{ $deg }}"
                                        class="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-500/30 dark:border-zinc-600 dark:bg-zinc-800" />
                                    {{ $deg }}
                                </label>
                            @endforeach
                        </div>

                        <div class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">
                                {{ __('Documents') }}
                            </p>
                            @foreach ([['portfolio', __('Portfolio')], ['certificate', __('Certificate')], ['paklaring', __('Paklaring')]] as [$val, $label])
                                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                                    <input type="checkbox" x-model="documents" value="{{ $val }}"
                                        class="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-500/30 dark:border-zinc-600 dark:bg-zinc-800" />
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    @if ($applications->isEmpty())
        <div
            class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-zinc-200 bg-zinc-50 p-16 dark:border-zinc-700 dark:bg-zinc-900/50">
            <flux:icon.document-text class="mb-6 h-16 w-16 text-zinc-300 dark:text-zinc-600" />
            <flux:heading size="lg" class="mb-2">{{ __('No Applicants Found') }}</flux:heading>
            <flux:text class="max-w-md text-center">{{ __('No applications match your current filters.') }}</flux:text>
        </div>
    @else
        <div class="glass-card-static overflow-hidden p-0!">
            <div class="overflow-x-auto">
                <table class="w-full text-sm modern-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th class="w-14 text-center!">{{ __('No.') }}</th>
                            <th>{{ __('Candidate') }}</th>
                            <th class="hidden md:table-cell">{{ __('Contact') }}</th>
                            <th class="text-center! hidden sm:table-cell">{{ __('Applied') }}</th>
                            <th class="text-center!">{{ __('Status') }}</th>
                            <th class="text-center! whitespace-nowrap w-px">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-800 dark:bg-zinc-900">
                        @foreach ($applications as $application)
                            @php
                                $stageColors = [
                                    'APPLIED' => 'zinc',
                                    'ADMINISTRASI' => 'sky',
                                    'HR_INTERVIEW' => 'yellow',
                                    'USER_INTERVIEW' => 'orange',
                                    'PSYCHOTEST' => 'purple',
                                    'OFFERING' => 'cyan',
                                    'MCU' => 'indigo',
                                    'ONBOARDING' => 'lime',
                                    'HIRED' => 'green',
                                    'REJECTED' => 'red',
                                ];
                                $color = $stageColors[$application->recruitment_stage->value] ?? 'zinc';
                                $isExpanded = $expandedRow === $application->id;
                                $lastPassedStage = $application->recruitment_stage === \App\Enums\RecruitmentStage::REJECTED
                                    ? $application->stageLogs->where('decision', 'passed')->last()?->stage?->label()
                                    : null;
                            @endphp
                            <tr wire:key="{{ $application->id }}" class="cursor-pointer">
                                <td class="px-6 py-4">
                                    <button wire:click="toggleExpand({{ $application->id }})" type="button"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-400 transition-all duration-200 hover:bg-zinc-100 hover:text-zinc-600 active:scale-95 dark:hover:bg-zinc-700/70 dark:hover:text-zinc-300"
                                        aria-label="{{ $isExpanded ? __('Collapse details') : __('Expand details') }}">
                                        <flux:icon.chevron-right
                                            class="size-4 transition-transform duration-300 ease-out {{ $isExpanded ? 'rotate-90' : '' }}" />
                                    </button>
                                </td>
                                <td class="px-3 py-4 text-center text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                                    {{ ($applications->firstItem() ?? 0) + $loop->index }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-zinc-900 dark:text-white">{{ $application->candidate->name }}
                                    </div>
                                    <div class="text-xs text-zinc-400">{{ $application->candidate->email }}</div>
                                </td>
                                <td class="hidden px-6 py-4 text-zinc-500 dark:text-zinc-400 md:table-cell">
                                    @if ($application->candidate->profile?->whatsapp)
                                        <div class="text-sm">{{ $application->candidate->profile->whatsapp }}</div>
                                    @else
                                        <span class="text-zinc-400">—</span>
                                    @endif
                                </td>
                                <td class="hidden px-6 py-4 text-center text-zinc-500 dark:text-zinc-400 sm:table-cell">
                                    {{ $application->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <flux:badge color="{{ $color }}" size="sm">{{ $application->recruitment_stage->label() }}
                                    </flux:badge>
                                    @if ($lastPassedStage)
                                        <p class="mt-1 text-xs text-zinc-400">{{ __('Last:') }} {{ $lastPassedStage }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap w-px">
                                    <flux:button href="{{ route('applications.review', [$job, $application]) }}" wire:navigate
                                        size="sm" variant="ghost" icon="eye">
                                        {{ __('Review') }}
                                    </flux:button>
                                </td>
                            </tr>

                            {{-- Expandable Row --}}
                            @if ($isExpanded && $expandedData?->id === $application->id)
                                @php $p = $expandedData->candidate->profile; @endphp
                                <tr wire:key="{{ $application->id }}-expanded" wire:transition.opacity.duration.200ms
                                    class="bg-zinc-50/50 dark:bg-zinc-800/30">
                                    <td colspan="7" class="px-6 py-4">
                                        <div x-data="{ show: false }" x-init="requestAnimationFrame(() => show = true)"
                                            x-show="show" x-transition:enter="transition ease-out duration-220"
                                            x-transition:enter-start="opacity-0 -translate-y-1"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm sm:grid-cols-3 lg:grid-cols-4">
                                            {{-- Gender --}}
                                            <div>
                                                <span
                                                    class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Gender') }}</span>
                                                <p class="mt-0.5 capitalize text-zinc-700 dark:text-zinc-300">
                                                    {{ $p?->gender ?? '—' }}
                                                </p>
                                            </div>
                                            {{-- Date of Birth + Age --}}
                                            <div>
                                                <span
                                                    class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Date of Birth') }}</span>
                                                <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">
                                                    @if ($p?->date_of_birth)
                                                        {{ $p->date_of_birth->format('d/m/y') }}
                                                        <span class="text-zinc-400">({{ $p->date_of_birth->age }}
                                                            {{ __('y.o.') }})</span>
                                                    @else
                                                        —
                                                    @endif
                                                </p>
                                            </div>
                                            {{-- Religion --}}
                                            <div>
                                                <span
                                                    class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Religion') }}</span>
                                                <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">{{ $p?->religion ?? '—' }}</p>
                                            </div>
                                            {{-- Marital Status --}}
                                            <div>
                                                <span
                                                    class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Marital Status') }}</span>
                                                <p class="mt-0.5 capitalize text-zinc-700 dark:text-zinc-300">
                                                    {{ $p?->marital_status ?? '—' }}
                                                </p>
                                            </div>
                                            {{-- NIK --}}
                                            <div>
                                                <span
                                                    class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('NIK') }}</span>
                                                <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">{{ $p?->nik ?? '—' }}</p>
                                            </div>
                                            {{-- Education --}}
                                            <div>
                                                <span
                                                    class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Education') }}</span>
                                                @php
                                                    $latestEdu = $expandedData->candidate->education
                                                        ->sortByDesc('end_year')->first();
                                                @endphp
                                                <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">
                                                    {{ $latestEdu ? $latestEdu->degree . ' — ' . $latestEdu->institution_name : '—' }}
                                                </p>
                                            </div>
                                            {{-- Work Experience --}}
                                            <div>
                                                <span
                                                    class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Work Exp.') }}</span>
                                                <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">
                                                    @if ($expandedData->candidate->experiences->isNotEmpty())
                                                        <span class="text-green-600 dark:text-green-400">✓
                                                            {{ $expandedData->candidate->experiences->count() }}
                                                            {{ __('job(s)') }}</span>
                                                    @else
                                                        <span class="text-zinc-400">{{ __('None') }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                            {{-- Organization --}}
                                            <div>
                                                <span
                                                    class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Organization') }}</span>
                                                <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">
                                                    @if ($expandedData->candidate->organizations->isNotEmpty())
                                                        <span class="text-green-600 dark:text-green-400">✓
                                                            {{ $expandedData->candidate->organizations->count() }}
                                                            {{ __('org(s)') }}</span>
                                                    @else
                                                        <span class="text-zinc-400">{{ __('None') }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                            {{-- Documents --}}
                                            <div class="col-span-2 sm:col-span-3 lg:col-span-4">
                                                <span
                                                    class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Documents') }}</span>
                                                <div class="mt-1 flex flex-wrap gap-1.5">
                                                    @if ($p?->ktp_path)
                                                        <flux:badge variant="outline" size="sm" icon="identification">{{ __('KTP') }}
                                                        </flux:badge>
                                                    @endif
                                                    @if ($p?->portfolio_path)
                                                        <flux:badge variant="outline" size="sm" icon="document">{{ __('Portfolio') }}
                                                        </flux:badge>
                                                    @endif
                                                    @if ($p?->certificate_path)
                                                        <flux:badge variant="outline" size="sm" icon="academic-cap">
                                                            {{ __('Certificate') }}
                                                        </flux:badge>
                                                    @endif
                                                    @if ($p?->paklaring_path)
                                                        <flux:badge variant="outline" size="sm" icon="document-text">
                                                            {{ __('Paklaring') }}
                                                        </flux:badge>
                                                    @endif
                                                    @if (!$p?->ktp_path && !$p?->portfolio_path && !$p?->certificate_path && !$p?->paklaring_path)
                                                        <span class="text-xs text-zinc-400">{{ __('No documents uploaded') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @elseif ($isExpanded)
                                <tr wire:key="{{ $application->id }}-loading" wire:transition.opacity.duration.150ms
                                    class="bg-zinc-50/50 dark:bg-zinc-800/30">
                                    <td colspan="7" class="px-6 py-4 text-sm text-zinc-400">
                                        <span wire:loading
                                            wire:target="toggleExpand({{ $application->id }})">{{ __('Loading...') }}</span>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $applications->links() }}</div>
    @endif

    {{-- ─── Bulk Email Modal ────────────────────────────────────────────────── --}}
    <flux:modal wire:model="showBulkEmailModal" class="w-full max-w-2xl">
        <div class="space-y-5">
            <div>
                <flux:heading size="lg">{{ __('Bulk Send Email') }}</flux:heading>
                <flux:text class="mt-1 text-sm text-zinc-500">{{ $job->title }}</flux:text>
            </div>

            @if ($bulkEmailStep === 1)
                {{-- Step 1: Compose --}}
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <flux:field class="flex-1">
                            <flux:label>{{ __('Target Stage') }}</flux:label>
                            @php
                                $bulkEmailStageOptions = ['' => __('All stages')];
                                foreach ($statuses as $stageStatus) {
                                    if ($stageStatus === \App\Enums\RecruitmentStage::REJECTED) {
                                        continue;
                                    }
                                    $bulkEmailStageOptions[$stageStatus->value] = $stageStatus->label();
                                }
                            @endphp
                            <x-custom-select wire:model.live="bulkEmailStage" placeholder="{{ __('All stages') }}"
                                :options="$bulkEmailStageOptions" />
                        </flux:field>
                        <flux:field>
                            <label class="flex cursor-pointer items-center gap-2 text-sm">
                                <flux:checkbox wire:model.live="bulkEmailActiveOnly" />
                                {{ __('Active only (exclude Rejected/Hired)') }}
                            </label>
                        </flux:field>
                    </div>

                    <div
                        class="rounded-lg bg-blue-50 px-4 py-2 text-sm text-blue-700 dark:bg-blue-900/20 dark:text-blue-300">
                        {{ __(':count recipients match these criteria.', ['count' => $bulkEmailCount]) }}
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Subject') }} *</flux:label>
                        <flux:input wire:model="bulkEmailSubject"
                            placeholder="{{ __('e.g. Undangan Psikotes — {job}') }}" />
                        <flux:error name="bulkEmailSubject" />
                        <flux:description>{{ __('Use {name} for candidate name, {job} for job title.') }}</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Email Body') }} *</flux:label>
                        <flux:textarea wire:model="bulkEmailBody" rows="8"
                            placeholder="{{ __('Dear {name},\n\nYou are invited to...') }}" />
                        <flux:error name="bulkEmailBody" />
                    </flux:field>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="$set('showBulkEmailModal', false)">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="button" wire:click="proceedBulkEmail" variant="primary" icon="arrow-right"
                        :disabled="$bulkEmailCount === 0">
                        {{ __('Preview & Confirm (:count)', ['count' => $bulkEmailCount]) }}
                    </flux:button>
                </div>

            @else
                {{-- Step 2: Confirm --}}
                <flux:callout variant="info" icon="information-circle">
                    <flux:callout.heading>{{ __('Sending to :count recipients', ['count' => $bulkEmailPreview->count()]) }}
                    </flux:callout.heading>
                    <flux:callout.text>{{ __('Subject:') }} {{ $bulkEmailSubject }}</flux:callout.text>
                </flux:callout>

                <div class="max-h-60 overflow-y-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <table class="w-full text-xs">
                        <thead class="sticky top-0 bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-zinc-500">{{ __('Name') }}</th>
                                <th class="px-3 py-2 text-left font-medium text-zinc-500">{{ __('Email') }}</th>
                                <th class="px-3 py-2 text-left font-medium text-zinc-500">{{ __('Stage') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach ($bulkEmailPreview as $app)
                                <tr>
                                    <td class="px-3 py-2">{{ $app->candidate->name }}</td>
                                    <td class="px-3 py-2 text-zinc-500">{{ $app->candidate->email }}</td>
                                    <td class="px-3 py-2 text-zinc-500">{{ $app->recruitment_stage->label() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($bulkEmailCount > 200)
                    <p class="text-xs text-zinc-400">
                        {{ __('...and :more more recipients not shown.', ['more' => $bulkEmailCount - 200]) }}
                    </p>
                @endif

                <div class="flex items-center justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="$set('bulkEmailStep', 1)">
                        {{ __('Back') }}
                    </flux:button>
                    <flux:button type="button" wire:click="sendBulkEmail" variant="primary" icon="paper-airplane"
                        wire:loading.attr="disabled" wire:target="sendBulkEmail">
                        <span wire:loading.remove
                            wire:target="sendBulkEmail">{{ __('Send to :count Recipients', ['count' => $bulkEmailCount]) }}</span>
                        <span wire:loading wire:target="sendBulkEmail">{{ __('Sending...') }}</span>
                    </flux:button>
                </div>
            @endif
        </div>
    </flux:modal>

    {{-- ─── Bulk Reject Modal ───────────────────────────────────────────────── --}}
    <flux:modal wire:model="showBulkRejectModal" class="w-full max-w-2xl">
        <div class="space-y-5">
            <div>
                <flux:heading size="lg">{{ __('Bulk Reject') }}</flux:heading>
                <flux:text class="mt-1 text-sm text-zinc-500">{{ $job->title }}</flux:text>
            </div>

            @if ($bulkRejectStep === 1)
                {{-- Step 1: Select threshold --}}
                <flux:callout variant="warning" icon="exclamation-triangle">
                    <flux:callout.heading>{{ __('How this works') }}</flux:callout.heading>
                    <flux:callout.text>
                        {{ __('Select the promotion stage. All active candidates BELOW that stage will be marked as Not Selected.') }}
                    </flux:callout.text>
                </flux:callout>

                <flux:field>
                    <flux:label>{{ __('Reject everyone below stage:') }} *</flux:label>
                    <x-custom-select wire:model.live="bulkRejectStage" placeholder="{{ __('Select a stage...') }}"
                        :options="['' => __('Select a stage...')] + collect($statuses)->filter(fn($s) => $s !== \App\Enums\RecruitmentStage::APPLIED && $s !== \App\Enums\RecruitmentStage::HIRED && $s !== \App\Enums\RecruitmentStage::REJECTED)->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()" />
                    <flux:description>{{ __('Example: select "Sourcing" to reject all candidates still at Registration.') }}
                    </flux:description>
                </flux:field>

                <div class="flex items-center justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="$set('showBulkRejectModal', false)">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="button" wire:click="proceedBulkRejectStep2" variant="primary">
                        {{ __('Preview') }}
                    </flux:button>
                </div>

            @elseif ($bulkRejectStep === 2)
                {{-- Step 2: Preview + Notes --}}
                <div class="flex gap-4">
                    <div class="flex-1 rounded-lg bg-red-50 p-3 text-center dark:bg-red-900/20">
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $bulkRejectCount }}</p>
                        <p class="text-xs text-red-500">{{ __('will be REJECTED') }}</p>
                    </div>
                    <div class="flex-1 rounded-lg bg-green-50 p-3 text-center dark:bg-green-900/20">
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $bulkRejectSafeCount }}</p>
                        <p class="text-xs text-green-500">{{ __('will NOT be affected') }}</p>
                    </div>
                </div>

                @if ($bulkRejectPreview->isNotEmpty())
                    <div class="max-h-48 overflow-y-auto rounded-lg border border-red-200 dark:border-red-800/50">
                        <table class="w-full text-xs">
                            <thead class="sticky top-0 bg-red-50 dark:bg-red-900/30">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium text-red-600">{{ __('Name') }}</th>
                                    <th class="px-3 py-2 text-left font-medium text-red-600">{{ __('Email') }}</th>
                                    <th class="px-3 py-2 text-left font-medium text-red-600">{{ __('Current Stage') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-red-100 dark:divide-red-900/20">
                                @foreach ($bulkRejectPreview as $app)
                                    <tr>
                                        <td class="px-3 py-2">{{ $app->candidate->name }}</td>
                                        <td class="px-3 py-2 text-zinc-500">{{ $app->candidate->email }}</td>
                                        <td class="px-3 py-2 text-zinc-500">{{ $app->recruitment_stage->label() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($bulkRejectCount > 50)
                        <p class="text-xs text-zinc-400">{{ __('...and :more more not shown.', ['more' => $bulkRejectCount - 50]) }}
                        </p>
                    @endif
                @endif

                <flux:field>
                    <flux:label>{{ __('Rejection Notes') }} <span
                            class="text-xs font-normal text-zinc-400">({{ __('optional') }})</span></flux:label>
                    <flux:textarea wire:model="bulkRejectNotes" rows="3"
                        placeholder="{{ __('Enter reason for bulk rejection...') }}" />
                    <flux:error name="bulkRejectNotes" />
                    <flux:description>
                        {{ __('If left empty, a default message will be used. A [Bulk Rejection] suffix is added automatically.') }}
                    </flux:description>
                </flux:field>

                <div class="flex items-center justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="$set('bulkRejectStep', 1)">
                        {{ __('Back') }}
                    </flux:button>
                    <flux:button type="button" wire:click="proceedBulkRejectStep3" variant="danger"
                        :disabled="$bulkRejectCount === 0">
                        {{ __('Proceed to Confirm') }}
                    </flux:button>
                </div>

            @else
                {{-- Step 3: Type REJECT --}}
                <flux:callout variant="danger" icon="exclamation-triangle">
                    <flux:callout.heading>{{ __('This action cannot be undone.') }}</flux:callout.heading>
                    <flux:callout.text>
                        {{ __(':count candidates will be permanently marked as Not Selected with a stage log entry.', ['count' => $bulkRejectCount]) }}
                    </flux:callout.text>
                </flux:callout>

                <flux:field>
                    <flux:label>{{ __('Type REJECT to confirm:') }}</flux:label>
                    <flux:input wire:model.live="bulkRejectConfirmText" placeholder="REJECT" />
                </flux:field>

                <div class="flex items-center justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="$set('bulkRejectStep', 2)">
                        {{ __('Back') }}
                    </flux:button>
                    <flux:button type="button" wire:click="executeBulkReject" variant="danger" wire:loading.attr="disabled"
                        wire:target="executeBulkReject" :disabled="$bulkRejectConfirmText !== 'REJECT'">
                        <span wire:loading.remove wire:target="executeBulkReject">{{ __('Execute Bulk Reject') }}</span>
                        <span wire:loading wire:target="executeBulkReject">{{ __('Processing...') }}</span>
                    </flux:button>
                </div>
            @endif
        </div>
    </flux:modal>
</div>
