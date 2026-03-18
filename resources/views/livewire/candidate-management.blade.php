<div class="flex flex-col gap-8">
    <div>
        @if ($tab === 'administrasi')
            <flux:heading size="xl" level="1">{{ __('Administrasi Kandidat') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Pengecekan berkas dan CV kandidat') }}</flux:subheading>
        @elseif ($tab === 'on-progress')
            <flux:heading size="xl" level="1">{{ __('Kandidat On Progress') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Kandidat yang sedang dalam proses rekrutmen') }}</flux:subheading>
        @else
            <flux:heading size="xl" level="1">{{ __('Riwayat Kandidat') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Seluruh riwayat kandidat yang pernah melamar') }}</flux:subheading>
        @endif
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>{{ session('success') }}</flux:callout.heading>
        </flux:callout>
    @endif

    <div class="glass-card-static p-4 sm:p-5">
        <div class="mb-3 flex items-center justify-between gap-2">
            <flux:heading size="sm">{{ __('Filters') }}</flux:heading>
            <flux:badge variant="outline">
                {{ $tab === 'administrasi' ? __('Administrasi') : ($tab === 'on-progress' ? __('On Progress') : __('Riwayat')) }}
            </flux:badge>
        </div>
        <div class="flex items-center gap-3">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search candidates...') }}"
                class="flex-1" icon="magnifying-glass" />
            <div class="w-24"><x-custom-select wire:model.live="perPage" :options="['10' => '10', '30' => '30', '50' => '50']" /></div>
        </div>
        <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
            <x-custom-select wire:model.live="filterDepartment" placeholder="{{ __('All Departments') }}"
                :options="['' => __('All Departments')] + $departments->pluck('name', 'id')->toArray()" />
            <x-custom-select wire:model.live="filterSite" placeholder="{{ __('All Sites') }}"
                :options="['' => __('All Sites')] + $sites->pluck('name', 'id')->toArray()" />
            <x-custom-select wire:model.live="filterStage" placeholder="{{ __('All Stages') }}"
                :options="['' => __('All Stages')] + collect($allStages)->mapWithKeys(fn($stage) => [$stage->value => $stage->label()])->toArray()" />
            @if ($tab === 'riwayat')
                <x-custom-select wire:model.live="filterStatus" placeholder="{{ __('All Statuses') }}"
                    :options="['' => __('All Statuses')] + collect($statusOptions)->mapWithKeys(fn($status) => [$status->value => $status->label()])->toArray()" />
            @endif
            <flux:button wire:click="resetDetailedFilters" variant="ghost" icon="x-mark" class="w-full">
                {{ __('Reset Filters') }}
            </flux:button>
        </div>
    </div>

    <div class="glass-card-static overflow-hidden p-0!">
        <table class="w-full text-sm modern-table">
            <thead>
                <tr>
                    <th class="w-16"></th>
                    <th>{{ __('Candidate Name') }}</th>
                    <th>{{ __('Applied Position') }}</th>
                    <th class="text-center!">{{ __('Apply Date') }}</th>
                    @if ($tab === 'on-progress')
                        <th class="text-center!">{{ __('Tgl. Lolos Admin') }}</th>
                    @else
                        <th class="text-center!">{{ __('Recruitment Stage') }}</th>
                    @endif
                    <th class="text-center!">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($applications as $application)
                    @php
                        $profile = $application->candidate->profile;
                        $isDocumentComplete = $profile && $profile->ktp_path && $profile->portfolio_path && $profile->certificate_path;
                        $isExpanded = $expandedRow === $application->id;
                        $isTerminal = $application->recruitment_stage->isTerminal();
                    @endphp
                    <tr class="transition-colors duration-200 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-6 py-4">
                            <button wire:click="toggleExpand({{ $application->id }})" type="button"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-400 transition-all duration-200 hover:bg-zinc-100 hover:text-zinc-600 active:scale-95 dark:hover:bg-zinc-700/70 dark:hover:text-zinc-300"
                                aria-label="{{ $isExpanded ? __('Collapse details') : __('Expand details') }}">
                                <flux:icon.chevron-right
                                    class="size-4 transition-transform duration-300 ease-out {{ $isExpanded ? 'rotate-90' : '' }}" />
                            </button>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold">{{ $application->candidate->name }}</div>
                            <div class="text-xs text-zinc-400">{{ $application->candidate->email }}</div>
                            @if ($application->candidate->profile?->whatsapp)
                                <div class="text-xs text-zinc-400">{{ $application->candidate->profile->whatsapp }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $application->job->title }}</td>
                        <td class="px-6 py-4 text-center">{{ $application->created_at->format('d M Y') }}</td>
                        {{-- Stage column with inline editing --}}
                        <td class="px-6 py-4 text-center">
                            @if ($tab === 'administrasi')
                                <div class="flex flex-col items-center gap-1">
                                    <flux:badge color="{{ $isDocumentComplete ? 'green' : 'amber' }}" size="sm">
                                        {{ $isDocumentComplete ? __('Berkas Lengkap') : __('Belum Lengkap') }}
                                    </flux:badge>
                                    <span class="text-xs text-zinc-400">{{ $application->recruitment_stage->label() }}</span>
                                </div>
                            @elseif ($tab === 'on-progress')
                                <span class="font-medium text-emerald-600 dark:text-emerald-400">
                                    {{ $application->stage_updated_at ? $application->stage_updated_at->format('d M Y') : '—' }}
                                </span>
                            @else
                                @if ($isTerminal)
                                    <flux:badge color="{{ $application->recruitment_stage === \App\Enums\RecruitmentStage::HIRED ? 'green' : 'red' }}" size="sm">
                                        {{ $application->recruitment_stage->label() }}
                                    </flux:badge>
                                @else
                                    <div class="w-44 inline-block">
                                        <x-custom-select
                                            wire:change="updateProgressStage({{ $application->id }}, $event.target.value)"
                                            :options="collect($allStages)->reject(fn($s) => $s->isTerminal())->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()" :placeholder="$application->recruitment_stage->label()" />
                                    </div>
                                @endif
                            @endif
                        </td>
                        {{-- Actions --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-wrap justify-center gap-2">
                                @if ($tab === 'administrasi')
                                    <flux:button size="sm" variant="ghost"
                                        @click="$dispatch('confirm-action', {
                                            title: 'Loloskan Administrasi?',
                                            description: 'Kandidat akan maju ke tahap HR Interview.',
                                            variant: 'info',
                                            method: 'passAdministrative',
                                            args: [{{ $application->id }}],
                                            confirmLabel: 'Ya, Loloskan'
                                        })">{{ __('Loloskan') }}</flux:button>
                                    <flux:button size="sm" variant="ghost" class="text-red-500 btn-danger-glow"
                                        @click="$dispatch('confirm-action', {
                                            title: 'Tolak Kandidat?',
                                            description: 'Kandidat akan ditandai sebagai tidak lolos administrasi.',
                                            variant: 'danger',
                                            method: 'rejectApplication',
                                            args: [{{ $application->id }}],
                                            confirmLabel: 'Ya, Tolak'
                                        })">{{ __('Tolak') }}</flux:button>
                                @elseif ($tab === 'on-progress')
                                    <flux:button size="sm" variant="primary" icon="calendar"
                                        wire:click="openScheduleInterview({{ $application->id }})">
                                        {{ __('Jadwalkan') }}
                                    </flux:button>
                                @endif
                                <flux:button size="sm" variant="ghost"
                                    href="{{ route('applications.review', [$application->job, $application]) }}"
                                    wire:navigate>{{ __('Detail') }}</flux:button>
                            </div>
                        </td>
                    </tr>

                    @if ($isExpanded)
                        <tr wire:key="candidate-{{ $application->id }}-expanded"
                            wire:transition.opacity.duration.200ms class="bg-zinc-50/50 dark:bg-zinc-800/30">
                            <td colspan="6" class="px-6 py-4">
                                <div x-data="{ show: false }" x-init="requestAnimationFrame(() => show = true)" x-show="show"
                                    x-transition:enter="transition ease-out duration-220"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm sm:grid-cols-3 lg:grid-cols-4">
                                    <div>
                                        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Gender') }}</span>
                                        <p class="mt-0.5 capitalize text-zinc-700 dark:text-zinc-300">{{ $profile?->gender ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Date of Birth') }}</span>
                                        <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">
                                            @if ($profile?->date_of_birth)
                                                {{ $profile->date_of_birth->format('d/m/y') }}
                                                <span class="text-zinc-400">({{ $profile->date_of_birth->age }} {{ __('y.o.') }})</span>
                                            @else
                                                —
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Religion') }}</span>
                                        <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">{{ $profile?->religion ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Marital Status') }}</span>
                                        <p class="mt-0.5 capitalize text-zinc-700 dark:text-zinc-300">{{ $profile?->marital_status ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('NIK') }}</span>
                                        <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">{{ $profile?->nik ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Education') }}</span>
                                        @php $latestEdu = $application->candidate->education->sortByDesc('end_year')->first(); @endphp
                                        <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">
                                            {{ $latestEdu ? $latestEdu->degree . ' — ' . $latestEdu->institution_name : '—' }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Work Exp.') }}</span>
                                        <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">
                                            @if ($application->candidate->experiences->isNotEmpty())
                                                <span class="text-green-600 dark:text-green-400">✓ {{ $application->candidate->experiences->count() }} {{ __('job(s)') }}</span>
                                            @else
                                                <span class="text-zinc-400">{{ __('None') }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Organization') }}</span>
                                        <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">
                                            @if ($application->candidate->organizations->isNotEmpty())
                                                <span class="text-green-600 dark:text-green-400">✓ {{ $application->candidate->organizations->count() }} {{ __('org(s)') }}</span>
                                            @else
                                                <span class="text-zinc-400">{{ __('None') }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-span-2 sm:col-span-3 lg:col-span-4">
                                        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Documents') }}</span>
                                        <div class="mt-1 flex flex-wrap gap-1.5">
                                            @if ($profile?->ktp_path)
                                                <flux:badge variant="outline" size="sm" icon="identification">{{ __('ID Card') }}</flux:badge>
                                            @endif
                                            @if ($profile?->portfolio_path)
                                                <flux:badge variant="outline" size="sm" icon="document">{{ __('Portfolio') }}</flux:badge>
                                            @endif
                                            @if ($profile?->certificate_path)
                                                <flux:badge variant="outline" size="sm" icon="academic-cap">{{ __('Certificate') }}</flux:badge>
                                            @endif
                                            @if ($profile?->paklaring_path)
                                                <flux:badge variant="outline" size="sm" icon="document-text">{{ __('Paklaring') }}</flux:badge>
                                            @endif
                                            @if (!$profile?->ktp_path && !$profile?->portfolio_path && !$profile?->certificate_path && !$profile?->paklaring_path)
                                                <span class="text-xs text-zinc-400">{{ __('No documents uploaded') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-zinc-400">{{ __('No candidates found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $applications->links() }}</div>

    <x-confirm-action />

    {{-- HR Interview Scheduling Modal --}}
    <flux:modal wire:model="showScheduleModal" class="w-full max-w-2xl">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Jadwalkan HR Interview') }}</flux:heading>
            <flux:subheading>{{ __('Jadwalkan tes wawancara HR untuk kandidat ini.') }}</flux:subheading>

            <form wire:submit="saveInterview" class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Interviewer (HR)') }}</flux:label>
                    <x-custom-select wire:model="interviewer_id" :options="['' => __('Pilih interviewer')] + $interviewers->mapWithKeys(fn($u) => [$u->id => $u->name])->toArray()" :searchable="true" />
                    <flux:error name="interviewer_id" />
                </flux:field>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Tanggal Interview') }}</flux:label>
                        <x-date-picker wire:model="scheduled_date" mode="date" placeholder="{{ __('Pilih tanggal...') }}" />
                        <flux:error name="scheduled_date" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('Waktu Interview') }}</flux:label>
                        <x-date-picker wire:model="scheduled_time" mode="time" placeholder="{{ __('Pilih waktu...') }}" />
                        <flux:error name="scheduled_time" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>{{ __('Catatan Khusus (Opsional)') }}</flux:label>
                    <flux:textarea rows="3" wire:model="hr_notes" placeholder="Catatan untuk interviewer..." />
                    <flux:error name="hr_notes" />
                </flux:field>

                <div class="flex justify-end gap-3 mt-2">
                    <flux:button type="button" variant="ghost" wire:click="$set('showScheduleModal', false)">{{ __('Batal') }}</flux:button>
                    <flux:button type="submit" variant="primary">{{ __('Simpan Jadwal') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>