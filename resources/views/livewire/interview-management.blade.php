<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ $tab === 'hr' ? __('Interview HR') : __('Interview User') }}
            </flux:heading>
            <flux:subheading size="lg">{{ __('Schedule and evaluate interviews') }}</flux:subheading>
        </div>
        <div>
            <flux:button wire:click="exportCsv" variant="ghost" icon="document-arrow-down">{{ __('Export CSV') }}
            </flux:button>
        </div>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>{{ session('success') }}</flux:callout.heading>
        </flux:callout>
    @endif

    <div class="glass-card-static p-4!">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
            <flux:field>
                <flux:label>{{ __('Search') }}</flux:label>
                <flux:input wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Candidate / email / position...') }}" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Department') }}</flux:label>
                <x-custom-select wire:model.live="filterDepartment" :options="['' => __('All departments')] + $departments->pluck('name', 'id')->toArray()" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Site') }}</flux:label>
                <x-custom-select wire:model.live="filterSite" :options="['' => __('All sites')] + $sites->pluck('name', 'id')->toArray()" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Status') }}</flux:label>
                <x-custom-select wire:model.live="filterStatus" :options="[
        '' => __('All status'),
        'scheduled' => __('Scheduled'),
        'completed' => __('Completed'),
        'passed' => __('Passed'),
        'failed' => __('Failed'),
    ]" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Rows') }}</flux:label>
                <x-custom-select wire:model.live="perPage" :options="[10 => '10', 20 => '20', 50 => '50', 100 => '100']" />
            </flux:field>
        </div>
    </div>

    <div class="glass-card-static overflow-hidden p-0!">
        <div class="overflow-x-auto">
            <table class="w-full text-sm modern-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center!">{{ __('No.') }}</th>
                        <th class="w-12"></th>
                        <th>{{ __('Candidate') }}</th>
                        <th>{{ __('Position') }}</th>
                        <th>{{ __('Interviewer') }}</th>
                        <th class="text-center!">{{ __('Interview Date') }}</th>
                        <th class="text-center!">{{ __('Status') }}</th>
                        <th class="text-center! whitespace-nowrap w-px">{{ __('Penilaian') }}</th>
                        <th class="text-center! whitespace-nowrap w-px">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @forelse ($interviews as $interview)
                        <tr wire:key="interview-{{ $interview->id }}">
                            <td class="px-4 py-3 text-center text-zinc-500 font-medium">
                                {{ ($interviews->currentPage() - 1) * $interviews->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-3">
                                @php $isExpanded = $expandedRow === $interview->application_id; @endphp
                                <button wire:click="toggleExpand({{ $interview->application_id }})" type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-400 transition-all duration-200 hover:bg-zinc-100 dark:hover:bg-zinc-700/70 hover:text-zinc-600 dark:hover:text-zinc-300 active:scale-95"
                                    aria-label="{{ $isExpanded ? __('Collapse details') : __('Expand details') }}">
                                    <flux:icon.chevron-right
                                        class="size-4 transition-transform duration-300 ease-out {{ $isExpanded ? 'rotate-90' : '' }}" />
                                </button>
                            </td>
                            <td class="px-6 py-4 font-semibold whitespace-nowrap">
                                {{ $interview->application->candidate->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $interview->application->job->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $interview->interviewer?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-center">{{ $interview->scheduled_at?->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusConfig = [
                                        'scheduled' => ['label' => 'Scheduled', 'badge' => 'text-blue-600 bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800/50 dark:text-blue-400', 'dot' => 'bg-blue-500'],
                                        'completed' => ['label' => 'Completed', 'badge' => 'text-zinc-600 bg-zinc-50 border-zinc-200 dark:bg-zinc-800/50 dark:border-zinc-700 dark:text-zinc-400', 'dot' => 'bg-zinc-400'],
                                        'passed' => ['label' => 'Passed', 'badge' => 'text-emerald-600 bg-emerald-50 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800/50 dark:text-emerald-400', 'dot' => 'bg-emerald-500'],
                                        'failed' => ['label' => 'Failed', 'badge' => 'text-red-600 bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800/50 dark:text-red-400', 'dot' => 'bg-red-500'],
                                    ];
                                    $current = $statusConfig[$interview->status] ?? $statusConfig['scheduled'];
                                @endphp
                                <x-custom-dropdown align="right" width="w-44">
                                    <x-slot name="trigger">
                                        <div
                                            class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg border cursor-pointer {{ $current['badge'] }}">
                                            <span class="size-2 rounded-full {{ $current['dot'] }}"></span>
                                            {{ $current['label'] }}
                                            <flux:icon.chevron-down class="size-3 opacity-60" />
                                        </div>
                                    </x-slot>

                                    <x-slot name="content">
                                        <div class="py-1">
                                            @foreach($statusConfig as $key => $cfg)
                                                @php
                                                    $needsFile = in_array($key, ['passed', 'failed']) && !$interview->evaluation_path;
                                                @endphp
                                                <button type="button" @if($needsFile) disabled
                                                    title="{{ __('Upload file penilaian terlebih dahulu sebelum mengubah status') }}"
                                                @else wire:click="updateInterviewStatus({{ $interview->id }}, '{{ $key }}')"
                                                    @endif
                                                    class="w-full flex items-center gap-2.5 px-4 py-2 text-sm {{ $interview->status === $key ? 'font-semibold text-brand-500' : 'text-zinc-700 dark:text-zinc-300' }} {{ $needsFile ? 'opacity-50 cursor-not-allowed' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">
                                                    <span class="size-2 rounded-full {{ $cfg['dot'] }}"></span>
                                                    {{ $cfg['label'] }}
                                                    @if($needsFile)
                                                        <flux:icon.lock-closed class="size-3.5 ml-auto text-zinc-400" />
                                                    @elseif($interview->status === $key)
                                                        <flux:icon.check class="size-4 ml-auto" />
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    </x-slot>
                                </x-custom-dropdown>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap w-px">
                                <div class="inline-flex flex-nowrap items-center justify-center gap-2 whitespace-nowrap">
                                    @if ($interview->evaluation_path)
                                        <a href="{{ Storage::url($interview->evaluation_path) }}" target="_blank"
                                            class="text-zinc-400 hover:text-brand-500 transition-colors"
                                            title="{{ __('View file') }}">
                                            <flux:icon.document-text class="size-5" />
                                        </a>
                                        <button type="button" class="text-zinc-400 hover:text-brand-500 transition-colors"
                                            wire:click="openUploadModal({{ $interview->id }})" title="{{ __('Update file') }}">
                                            <flux:icon.arrow-up-tray class="size-5" />
                                        </button>
                                    @else
                                        <button type="button" class="text-zinc-400 hover:text-brand-500 transition-colors"
                                            wire:click="openUploadModal({{ $interview->id }})" title="{{ __('Upload file') }}">
                                            <flux:icon.arrow-up-tray class="size-5" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap w-px">
                                <div class="inline-flex flex-nowrap items-center justify-center gap-2 whitespace-nowrap">
                                    <flux:button size="sm" variant="ghost" wire:click="openEdit({{ $interview->id }})"
                                        wire:target="openEdit({{ $interview->id }})" icon="pencil" class="app-action-btn">
                                        {{ __('Edit') }}
                                    </flux:button>

                                    @if ($tab === 'hr' && $interview->status === 'passed')
                                        @php
                                            $app = $applications->firstWhere('id', $interview->application_id);
                                            $hasUserInterview = $app ? $app->interviews->contains('interview_type', 'User Interview') : false;
                                        @endphp
                                        @if (!$hasUserInterview)
                                            @if ($interview->evaluation_path)
                                                <flux:button size="sm" variant="primary"
                                                    wire:click="openScheduleUserInterview({{ $interview->application_id }})"
                                                    wire:target="openScheduleUserInterview({{ $interview->application_id }})">
                                                    {{ __('Jadwalkan Int. User') }}
                                                </flux:button>
                                            @else
                                                <div class="text-xs text-orange-500 w-24 leading-tight font-medium"
                                                    title="{{ __('Unggah dokumen pada kolom Penilaian') }}">
                                                    {{ __('Menunggu file penilaian') }}
                                                </div>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @if ($expandedRow === $interview->application_id)
                            <tr wire:key="interview-candidate-{{ $interview->application_id }}-expanded"
                                wire:transition.opacity.duration.200ms class="bg-zinc-50/50 dark:bg-zinc-800/30">
                                <td colspan="9" class="px-6 py-4">
                                    <x-candidate-expanded-row :application="$interview->application" />
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-zinc-400">{{ __('No interview data yet.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $interviews->links() }}</div>

    <flux:modal wire:model="showModal" class="w-full max-w-2xl">
        <div class="space-y-4">
            <flux:heading size="lg">{{ $editingId ? __('Update Interview') : __('Schedule Interview') }}</flux:heading>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Candidate & Position') }}</flux:label>
                        <div
                            class="px-3 py-2 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-white/10 text-sm font-medium text-zinc-600 dark:text-zinc-300">
                            @php
                                $lockedApp = $this->lockedApplication;
                                $lockedLabel = $lockedApp ? $lockedApp->candidate->name . ' - ' . $lockedApp->job->title : '—';
                            @endphp
                            {{ $lockedLabel }}
                        </div>
                    </flux:field>

                    <flux:field>
                        <flux:label>
                            {{ $interview_type === 'HR Interview' ? __('Interviewer (HR)') : __('Interviewer (User)') }}
                        </flux:label>
                        <x-custom-select wire:model="interviewer_id" :options="['' => __('Select interviewer')] + $interviewers->mapWithKeys(fn($u) => [$u->id => $u->name])->toArray()" :searchable="true" />
                        <flux:error name="interviewer_id" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 {{ $editingId ? 'sm:grid-cols-2' : '' }} gap-4">
                    <flux:field>
                        <flux:label>{{ __('Interview Type') }}</flux:label>
                        <div
                            class="px-3 py-2 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-white/10 text-sm font-medium text-zinc-600 dark:text-zinc-300">
                            {{ $interview_type }}
                        </div>
                    </flux:field>
                    @if($editingId)
                        <flux:field>
                            <flux:label>{{ __('Status') }}</flux:label>
                            <x-custom-select wire:model="status" :options="['scheduled' => 'Scheduled', 'completed' => 'Completed', 'passed' => 'Passed', 'failed' => 'Failed']" />
                            <flux:error name="status" />
                        </flux:field>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Interview Date') }}</flux:label>
                        <x-date-picker wire:model="scheduled_date" mode="date"
                            placeholder="{{ __('Select date...') }}" />
                        <flux:error name="scheduled_date" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('Interview Time') }}</flux:label>
                        <x-date-picker wire:model="scheduled_time" mode="time"
                            placeholder="{{ __('Select time...') }}" />
                        <flux:error name="scheduled_time" />
                    </flux:field>
                </div>

                @if($editingId)
                    <flux:field>
                        <flux:label>{{ __('Dokumen Penilaian (PDF/DOCX/Gambar)') }}</flux:label>
                        @if($editingId && ($editedInterview = $interviews->firstWhere('id', $editingId)) && $editedInterview->evaluation_path)
                            <a href="{{ Storage::url($editedInterview->evaluation_path) }}" target="_blank"
                                class="mb-2 inline-flex items-center gap-1 text-sm text-brand-500 hover:underline">
                                <flux:icon.document-text class="size-4" /> {{ __('Lihat Dokumen Saat Ini') }}
                            </a>
                        @endif
                        <input type="file" wire:model="evaluation_file"
                            wire:key="interview-evaluation-file-{{ $editingId ?? 'new' }}"
                            accept=".pdf,.docx,.jpg,.jpeg,.png"
                            class="block w-full text-sm text-zinc-600 dark:text-zinc-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-300 dark:hover:file:bg-zinc-700 focus:outline-none cursor-pointer" />
                        <div wire:loading wire:target="evaluation_file" class="mt-2 text-sm text-brand-500">
                            {{ __('Uploading...') }}
                        </div>
                        <flux:error name="evaluation_file" />
                    </flux:field>
                @endif

                <flux:field>
                    <flux:label>{{ __('Catatan Interview') }}</flux:label>
                    <flux:textarea rows="4" wire:model="hr_notes" />
                    <flux:error name="hr_notes" />
                </flux:field>

                <div class="flex justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="$set('showModal', false)">{{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                        wire:target="save,evaluation_file">
                        <span wire:loading.remove wire:target="save">{{ __('Save') }}</span>
                        <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- Evaluation Upload Modal --}}
    <flux:modal wire:model="showUploadModal" class="w-full max-w-md">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Upload Penilaian Interview') }}</flux:heading>
            <flux:subheading>{{ __('Silakan unggah dokumen hasil wawancara (PDF/DOCX/Gambar).') }}</flux:subheading>

            <form wire:submit="saveUpload" class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Dokumen Penilaian') }}</flux:label>
                    <input type="file" wire:model="upload_file"
                        wire:key="interview-upload-file-{{ $uploadingInterviewId ?? 'new' }}"
                        accept=".pdf,.docx,.jpg,.jpeg,.png"
                        class="block w-full text-sm text-zinc-600 dark:text-zinc-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-300 dark:hover:file:bg-zinc-700 focus:outline-none cursor-pointer" />
                    <div wire:loading wire:target="upload_file" class="mt-2 text-sm text-brand-500">
                        {{ __('Uploading...') }}
                    </div>
                    <flux:error name="upload_file" />
                </flux:field>

                <div class="flex justify-end gap-3 mt-4">
                    <flux:button type="button" variant="ghost" wire:click="$set('showUploadModal', false)">
                        {{ __('Batal') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                        wire:target="saveUpload,upload_file">
                        <span wire:loading.remove wire:target="saveUpload">{{ __('Upload') }}</span>
                        <span wire:loading wire:target="saveUpload">{{ __('Uploading...') }}</span>
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
