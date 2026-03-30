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
        <div class="flex flex-col gap-3 md:flex-row md:items-center">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                placeholder="{{ __('Search candidates...') }}" class="w-full md:w-64" />
            <flux:button wire:click="exportCsv" variant="ghost" icon="document-arrow-down" class="max-md:w-full">{{ __('Export CSV') }}</flux:button>
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

    @if(count($selectedIds) > 0)
        <div class="mb-4 flex flex-wrap items-center gap-3 bg-blue-50/50 dark:bg-blue-900/20 p-3 rounded-xl border border-blue-100 dark:border-blue-800/30">
            <span class="text-sm font-medium text-blue-700 dark:text-blue-400">{{ count($selectedIds) }} {{ __('kandidat terpilih') }}</span>
            @if ($tab === 'administrasi')
                <flux:button size="sm" variant="primary"
                    @click="$dispatch('confirm-action', {
                        title: 'Loloskan Kandidat Terpilih?',
                        description: '{{ count($selectedIds) }} kandidat akan dipindahkan ke tahap On Progress (Interview HR).',
                        variant: 'info',
                        method: 'bulkPassAdministrative',
                        confirmLabel: 'Ya, Loloskan Semua'
                    })"
                >{{ __('Loloskan Terpilih') }}</flux:button>
                
                <flux:button size="sm" variant="danger" class="btn-danger-glow"
                    @click="$dispatch('confirm-action', {
                        title: 'Tolak Kandidat Terpilih?',
                        description: '{{ count($selectedIds) }} kandidat akan ditandai sebagai tidak lolos.',
                        variant: 'danger',
                        method: 'bulkReject',
                        confirmLabel: 'Ya, Tolak Semua'
                    })"
                >{{ __('Tolak') }}</flux:button>
            @endif
        </div>
    @endif

    <div class="glass-card-static overflow-hidden p-0!">
        <table class="w-full text-sm modern-table">
            <thead>
                <tr>
                    @if ($tab === 'administrasi')
                        <th class="w-12 px-4 py-3 text-center">
                            <flux:checkbox wire:model.live="selectAll" />
                        </th>
                    @endif
                    <th class="w-12 text-center!">{{ __('No.') }}</th>
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
                    <tr class="cursor-pointer">
                        @if ($tab === 'administrasi')
                            <td class="w-12 px-4 py-3 text-center" @click.stop>
                                <flux:checkbox wire:model.live="selectedIds" value="{{ $application->id }}" />
                            </td>
                        @endif
                        <td class="px-4 py-3 text-center text-zinc-500 font-medium">
                            {{ ($applications->currentPage() - 1) * $applications->perPage() + $loop->iteration }}
                        </td>
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
                                @if ($isTerminal && !auth()->user()->isSuperAdmin())
                                    <flux:badge color="{{ $application->recruitment_stage === \App\Enums\RecruitmentStage::HIRED ? 'green' : 'red' }}" size="sm">
                                        {{ $application->recruitment_stage->label() }}
                                    </flux:badge>
                                @else
                                    @php
                                        $stageConfig = [
                                            'APPLIED'        => ['label' => 'Applied', 'badge' => 'text-zinc-600 bg-zinc-50 border-zinc-200 dark:bg-zinc-800/50 dark:border-zinc-700 dark:text-zinc-400', 'dot' => 'bg-zinc-400'],
                                            'HR_INTERVIEW'   => ['label' => 'HR Interview', 'badge' => 'text-amber-600 bg-amber-50 border-amber-200 dark:bg-amber-900/20 dark:border-amber-800/50 dark:text-amber-400', 'dot' => 'bg-amber-500'],
                                            'USER_INTERVIEW' => ['label' => 'User Interview', 'badge' => 'text-orange-600 bg-orange-50 border-orange-200 dark:bg-orange-900/20 dark:border-orange-800/50 dark:text-orange-400', 'dot' => 'bg-orange-500'],
                                            'OFFERING'       => ['label' => 'Offering', 'badge' => 'text-cyan-600 bg-cyan-50 border-cyan-200 dark:bg-cyan-900/20 dark:border-cyan-800/50 dark:text-cyan-400', 'dot' => 'bg-cyan-500'],
                                            'PSYCHOTEST'     => ['label' => 'Psychotest', 'badge' => 'text-purple-600 bg-purple-50 border-purple-200 dark:bg-purple-900/20 dark:border-purple-800/50 dark:text-purple-400', 'dot' => 'bg-purple-500'],
                                            'MCU'            => ['label' => 'MCU', 'badge' => 'text-indigo-600 bg-indigo-50 border-indigo-200 dark:bg-indigo-900/20 dark:border-indigo-800/50 dark:text-indigo-400', 'dot' => 'bg-indigo-500'],
                                            'ONBOARDING'     => ['label' => 'Onboarding', 'badge' => 'text-lime-600 bg-lime-50 border-lime-200 dark:bg-lime-900/20 dark:border-lime-800/50 dark:text-lime-400', 'dot' => 'bg-lime-500'],
                                            'HIRED'          => ['label' => 'Hired', 'badge' => 'text-emerald-600 bg-emerald-50 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800/50 dark:text-emerald-400', 'dot' => 'bg-emerald-500'],
                                            'REJECTED'       => ['label' => 'Rejected', 'badge' => 'text-red-600 bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800/50 dark:text-red-400', 'dot' => 'bg-red-500'],
                                        ];
                                        $currentStage = $stageConfig[$application->recruitment_stage->value] ?? $stageConfig['APPLIED'];
                                    @endphp
                                    <x-custom-dropdown align="right" width="w-48">
                                        <x-slot name="trigger">
                                            <div class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg border cursor-pointer {{ $currentStage['badge'] }}">
                                                <span class="size-2 rounded-full {{ $currentStage['dot'] }}"></span>
                                                {{ $currentStage['label'] }}
                                                <flux:icon.chevron-down class="size-3 opacity-60" />
                                            </div>
                                        </x-slot>

                                        <x-slot name="content">
                                            <div class="py-1">
                                                @foreach($stageConfig as $key => $cfg)
                                                    @php
                                                        $isCurrent = $application->recruitment_stage->value === $key;
                                                        $isPreceding = array_search($key, array_keys($stageConfig)) < array_search($application->recruitment_stage->value, array_keys($stageConfig));
                                                        $isRejected = $key === 'REJECTED';
                                                        
                                                        // Determine variant and message
                                                        $variant = 'info';
                                                        $title = 'Ubah Tahapan Kandidat?';
                                                        $desc = "Pindahkan kandidat ke tahap {$cfg['label']}.";
                                                        $btnLabel = 'Ya, Ubah';
                                                        
                                                        if ($isRejected) {
                                                            $variant = 'danger';
                                                            $title = 'Tolak Kandidat?';
                                                            $desc = 'Kandidat akan ditandai sebagai tidak lolos rekrutmen.';
                                                            $btnLabel = 'Ya, Tolak';
                                                        } elseif ($isPreceding) {
                                                            $variant = 'warning';
                                                            $title = 'Turunkan Tahapan?';
                                                            $desc = "Anda memindahkan kandidat MUNDUR ke tahap {$cfg['label']}. Apakah Anda yakin?";
                                                        }
                                                    @endphp
                                                    <button type="button"
                                                        @if(!$isCurrent)
                                                            @click="$dispatch('confirm-action', {
                                                                title: '{{ $title }}',
                                                                description: '{{ $desc }}',
                                                                variant: '{{ $variant }}',
                                                                method: 'updateProgressStage',
                                                                args: [{{ $application->id }}, '{{ $key }}'],
                                                                confirmLabel: '{{ $btnLabel }}'
                                                            })"
                                                        @endif
                                                        class="w-full flex items-center gap-2.5 px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800 {{ $isCurrent ? 'font-semibold text-brand-500 cursor-default' : 'text-zinc-700 dark:text-zinc-300' }}">
                                                        <span class="size-2 rounded-full {{ $cfg['dot'] }}"></span>
                                                        {{ $cfg['label'] }}
                                                        @if($isCurrent)
                                                            <flux:icon.check class="size-4 ml-auto" />
                                                        @endif
                                                    </button>
                                                @endforeach
                                            </div>
                                        </x-slot>
                                    </x-custom-dropdown>
                                @endif
                            @endif
                        </td>
                        {{-- Actions --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-wrap justify-center gap-2">
                                @if ($tab === 'administrasi')
                                    <flux:button size="sm" variant="ghost"
                                        @click="$dispatch('confirm-action', {
                                            title: 'Loloskan Kandidat?',
                                            description: 'Kandidat akan masuk ke tahap On Progress (Interview HR).',
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
                                        wire:click="openScheduleInterview({{ $application->id }})"
                                        wire:target="openScheduleInterview({{ $application->id }})">
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
                            <td colspan="7" class="px-6 py-4">
                                <x-candidate-expanded-row :application="$application" />
                            </td>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-zinc-400">{{ __('No candidates found.') }}</td>
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
