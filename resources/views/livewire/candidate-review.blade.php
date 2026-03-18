<div class="flex flex-col gap-8">
    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ $application->candidate->name }}</flux:heading>
            <flux:subheading size="lg">{{ $job->title }} · {{ $job->department?->name }}</flux:subheading>
        </div>
        <div class="flex items-center gap-3">
            @if(in_array($application->recruitment_stage, [\App\Enums\RecruitmentStage::HR_INTERVIEW, \App\Enums\RecruitmentStage::USER_INTERVIEW]))
                @livewire('interview-scheduler', ['application' => $application])
            @endif
            <flux:button href="{{ route('applications.job', $job) }}" wire:navigate variant="ghost" icon="arrow-left"
                size="sm">
                {{ __('Back to Applicants') }}
            </flux:button>
        </div>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>{{ session('success') }}</flux:callout.heading>
        </flux:callout>
    @endif

    <flux:separator variant="subtle" />

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        {{-- Left: CV --}}
        <div class="flex flex-col gap-6 lg:col-span-2">
            @php $profile = $application->candidate->profile; @endphp

            {{-- Personal Info --}}
            <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                <div class="mb-4 flex items-start gap-4">
                    @if ($profile?->photo_path)
                        <img src="{{ Storage::url($profile->photo_path) }}" alt="Photo"
                            class="h-20 w-20 rounded-xl object-cover">
                    @else
                        <div class="flex h-20 w-20 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                            <flux:icon.user class="h-10 w-10 text-zinc-400" />
                        </div>
                    @endif
                    <div>
                        <flux:heading size="lg">{{ $application->candidate->name }}</flux:heading>
                        <p class="text-sm text-zinc-500">{{ $application->candidate->email }}</p>
                        @if ($profile?->whatsapp)
                            <p class="text-sm text-zinc-500">WA: {{ $profile->whatsapp }}</p>
                        @endif
                        @if ($profile?->linkedin_url)
                            <a href="{{ $profile->linkedin_url }}" target="_blank"
                                class="text-sm text-blue-500 hover:underline">LinkedIn</a>
                        @endif
                    </div>
                </div>

                @if ($profile)
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="font-medium text-zinc-600 dark:text-zinc-400">{{ __('NIK') }}:</span>
                            <span class="ml-1">{{ $profile->nik }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-zinc-600 dark:text-zinc-400">{{ __('Gender') }}:</span>
                            <span class="ml-1 capitalize">{{ $profile->gender }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-zinc-600 dark:text-zinc-400">{{ __('Date of Birth') }}:</span>
                            <span class="ml-1">{{ $profile->date_of_birth?->format('d M Y') }}
                                ({{ $profile->place_of_birth }})</span>
                        </div>
                        <div>
                            <span class="font-medium text-zinc-600 dark:text-zinc-400">{{ __('Religion') }}:</span>
                            <span class="ml-1">{{ $profile->religion }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-zinc-600 dark:text-zinc-400">{{ __('Marital Status') }}:</span>
                            <span class="ml-1 capitalize">{{ $profile->marital_status }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="font-medium text-zinc-600 dark:text-zinc-400">{{ __('ID Card Address') }}:</span>
                            <span class="ml-1">{{ $profile->address_ktp }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="font-medium text-zinc-600 dark:text-zinc-400">{{ __('Domicile Address') }}:</span>
                            <span class="ml-1">{{ $profile->address_domicile }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Education --}}
            @if ($application->candidate->education->isNotEmpty())
                <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                    <flux:heading size="md" class="mb-4">{{ __('Education') }}</flux:heading>
                    <div class="flex flex-col gap-3">
                        @foreach ($application->candidate->education as $edu)
                            <div class="border-l-2 border-blue-400 pl-4">
                                <p class="font-semibold">{{ $edu->degree }} — {{ $edu->institution_name }}</p>
                                <p class="text-sm text-zinc-500">{{ $edu->major }}</p>
                                <p class="text-xs text-zinc-400">
                                    {{ $edu->start_year }} – {{ $edu->end_year ?? __('Present') }}
                                    @if ($edu->gpa) · GPA: {{ $edu->gpa }} @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Work Experience --}}
            @if ($application->candidate->experiences->isNotEmpty())
                <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                    <flux:heading size="md" class="mb-4">{{ __('Work Experience') }}</flux:heading>
                    <div class="flex flex-col gap-4">
                        @foreach ($application->candidate->experiences as $exp)
                            <div class="border-l-2 border-green-400 pl-4">
                                <p class="font-semibold">{{ $exp->position }}</p>
                                <p class="text-sm text-zinc-500">{{ $exp->company_name }}</p>
                                <p class="text-xs text-zinc-400">
                                    {{ $exp->start_date?->format('M Y') }} –
                                    {{ $exp->is_current ? __('Present') : $exp->end_date?->format('M Y') }}
                                    @if ($exp->last_salary) · Last Salary: Rp
                                    {{ number_format($exp->last_salary, 0, ',', '.') }} @endif
                                </p>
                                @if ($exp->job_description)
                                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $exp->job_description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Organizations --}}
            @if ($application->candidate->organizations->isNotEmpty())
                <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                    <flux:heading size="md" class="mb-4">{{ __('Organizational Experience') }}</flux:heading>
                    <div class="flex flex-col gap-3">
                        @foreach ($application->candidate->organizations as $org)
                            <div class="border-l-2 border-purple-400 pl-4">
                                <p class="font-semibold">{{ $org->position }}</p>
                                <p class="text-sm text-zinc-500">{{ $org->organization_name }}</p>
                                <p class="text-xs text-zinc-400">
                                    {{ $org->start_date?->format('M Y') }} –
                                    {{ $org->is_current ? __('Present') : $org->end_date?->format('M Y') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Documents --}}
            @if ($profile)
                <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                    <flux:heading size="md" class="mb-4">{{ __('Documents') }}</flux:heading>
                    <div class="flex flex-wrap gap-3">
                        @if ($profile->ktp_path)
                            <a href="{{ Storage::url($profile->ktp_path) }}" target="_blank">
                                <flux:button variant="ghost" icon="identification" size="sm">{{ __('ID Card') }}</flux:button>
                            </a>
                        @endif
                        @if ($profile->portfolio_path)
                            <a href="{{ Storage::url($profile->portfolio_path) }}" target="_blank">
                                <flux:button variant="ghost" icon="document" size="sm">{{ __('Portfolio') }}</flux:button>
                            </a>
                        @endif
                        @if ($profile->certificate_path)
                            <a href="{{ Storage::url($profile->certificate_path) }}" target="_blank">
                                <flux:button variant="ghost" icon="academic-cap" size="sm">{{ __('Certificate') }}</flux:button>
                            </a>
                        @endif
                        @if ($profile->paklaring_path)
                            <a href="{{ Storage::url($profile->paklaring_path) }}" target="_blank">
                                <flux:button variant="ghost" icon="document-text" size="sm">{{ __('Paklaring') }}</flux:button>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Right: Stage Pipeline + Decision --}}
        <div class="flex flex-col gap-6">

            {{-- Full Stage Pipeline --}}
            <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                <flux:heading size="md" class="mb-5">{{ __('Progress Lamaran') }}</flux:heading>
                @php
                    $allStages = \App\Enums\RecruitmentStage::pipelineStages();
                    $logMap = $application->stageLogs->keyBy(fn($l) => $l->stage instanceof \App\Enums\RecruitmentStage ? $l->stage->value : $l->stage);
                    $isRejected = $application->recruitment_stage === \App\Enums\RecruitmentStage::REJECTED;
                    $currentIndex = array_search($application->recruitment_stage, $allStages);
                @endphp
                <div class="flex flex-col">
                    @foreach ($allStages as $i => $stage)
                        @php
                            $log = $logMap->get($stage->value);
                            $stageIndex = $i;
                            $isPastByProgress = !$isRejected && $currentIndex !== false && $stageIndex < $currentIndex;
                            $isPassed = ($log && $log->decision === 'passed') || $isPastByProgress;
                            $isRejectedHere = $log && $log->decision === 'rejected';
                            $isCurrent = !$isRejected && $currentIndex === $stageIndex;
                            $isUpcoming = !$isRejected && $currentIndex !== false && $stageIndex > $currentIndex;
                        @endphp

                        <div class="flex gap-3">
                            {{-- Connector line + circle column --}}
                            <div class="flex flex-col items-center">
                                {{-- Top line (except first) --}}
                                @if ($i > 0)
                                    <div
                                        class="w-px flex-none h-3 {{ $isPassed || $isRejectedHere ? 'bg-green-400' : ($isCurrent ? 'bg-blue-400' : 'bg-zinc-200 dark:bg-zinc-700') }}">
                                    </div>
                                @else
                                    <div class="h-3"></div>
                                @endif

                                {{-- Circle --}}
                                <div
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-semibold
                                            {{ $isPassed ? 'bg-green-500 text-white' : ($isRejectedHere ? 'bg-red-500 text-white' : ($isCurrent ? 'bg-blue-500 text-white ring-4 ring-blue-500/20' : 'bg-zinc-200 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400')) }}">
                                    @if ($isPassed)
                                        <flux:icon.check class="size-4" />
                                    @elseif ($isRejectedHere)
                                        <flux:icon.x-mark class="size-4" />
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </div>

                                {{-- Bottom line (except last) --}}
                                @if (!$loop->last)
                                    <div
                                        class="w-px flex-1 min-h-3 {{ $isPassed ? 'bg-green-400' : 'bg-zinc-200 dark:bg-zinc-700' }}">
                                    </div>
                                @endif
                            </div>

                            {{-- Stage content --}}
                            <div class="pb-4 pt-3 flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span
                                        class="text-sm font-semibold
                                                {{ $isPassed ? 'text-green-700 dark:text-green-400' : ($isRejectedHere ? 'text-red-700 dark:text-red-400' : ($isCurrent ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 dark:text-zinc-500')) }}">
                                        {{ $stage->label() }}
                                    </span>
                                    @if ($isCurrent)
                                        <flux:badge color="blue" size="sm">{{ __('Tahap Saat Ini') }}</flux:badge>
                                    @elseif ($isPassed)
                                        <flux:badge color="green" size="sm">{{ __('Lolos') }}</flux:badge>
                                    @elseif ($isRejectedHere)
                                        <flux:badge color="red" size="sm">{{ __('Tidak Lolos') }}</flux:badge>
                                    @endif
                                </div>

                                @if ($log)
                                    <div
                                        class="mt-1.5 rounded-lg p-2.5 text-xs {{ $isPassed ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                                        @if ($log->notes)
                                            <p class="text-zinc-700 dark:text-zinc-300 mb-1">{{ $log->notes }}</p>
                                        @endif
                                        <p class="text-zinc-400">
                                            {{ $log->created_at->format('d M Y H:i:s') }}
                                            · {{ __('by') }} {{ $log->decidedBy->name }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Decision Form --}}
            @if ($application->canAdvance())
                <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                    <flux:heading size="md" class="mb-4">{{ __('Keputusan') }}</flux:heading>

                    <flux:field class="mb-4">
                        <flux:label>{{ __('Notes') }} <span
                                class="text-xs font-normal text-zinc-400">({{ __('optional') }})</span></flux:label>
                        <flux:textarea wire:model="notes" rows="4"
                            placeholder="{{ __('Tambahkan catatan keputusan (opsional)...') }}" />
                        <flux:error name="notes" />
                    </flux:field>

                    <div class="flex flex-col gap-2">
                        @if ($application->nextStage())
                            <flux:button
                                @click="$dispatch('confirm-action', {
                                    title: 'Loloskan Kandidat?',
                                    description: 'Kandidat akan dilanjutkan ke tahap {{ $application->nextStage()->label() }}.',
                                    variant: 'info',
                                    method: 'advance',
                                    confirmLabel: 'Ya, Loloskan'
                                })"
                                variant="primary" icon="check"
                                class="w-full bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600">
                                {{ __('Loloskan ke') }}: {{ $application->nextStage()->label() }}
                            </flux:button>
                        @endif
                        <flux:button
                            @click="$dispatch('confirm-action', {
                                title: 'Tandai Tidak Lolos?',
                                description: 'Kandidat akan ditandai sebagai tidak lolos. Aksi ini tidak dapat dibatalkan.',
                                variant: 'danger',
                                method: 'reject',
                                confirmLabel: 'Ya, Tidak Lolos'
                            })"
                            variant="danger" icon="x-mark"
                            class="w-full btn-danger-glow bg-red-600 text-white hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600">
                            {{ __('Tandai Tidak Lolos') }}
                        </flux:button>
                    </div>
                </div>
            @else
                <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                    <p class="text-sm text-zinc-500">
                        @if ($application->recruitment_stage === \App\Enums\RecruitmentStage::HIRED)
                            {{ __('This candidate has been hired.') }}
                        @else
                            {{ __('This application has been closed.') }}
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <x-confirm-action />
</div>