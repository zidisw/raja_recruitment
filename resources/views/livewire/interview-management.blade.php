<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ $tab === 'hr' ? __('Interview HR') : __('Interview User') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Schedule and evaluate interviews') }}</flux:subheading>
        </div>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>{{ session('success') }}</flux:callout.heading>
        </flux:callout>
    @endif

    <div class="glass-card-static overflow-hidden p-0!">
        <table class="w-full text-sm modern-table">
            <thead>
                <tr>
                    <th>{{ __('Candidate') }}</th>
                    <th>{{ __('Position') }}</th>
                    <th>{{ __('Interviewer') }}</th>
                    <th class="text-center!">{{ __('Interview Date') }}</th>
                    <th class="text-center!">{{ __('Status') }}</th>
                    <th class="text-center!">{{ __('Penilaian') }}</th>
                    <th class="text-center!">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($interviews as $interview)
                    <tr>
                        <td class="px-6 py-4 font-semibold">{{ $interview->application->candidate->name }}</td>
                        <td class="px-6 py-4">{{ $interview->application->job->title }}</td>
                        <td class="px-6 py-4">{{ $interview->interviewer?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-center">{{ $interview->scheduled_at?->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusColors = [
                                    'scheduled' => 'text-blue-600 bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800/50 dark:text-blue-400',
                                    'completed' => 'text-zinc-600 bg-zinc-50 border-zinc-200 dark:bg-zinc-800/50 dark:border-zinc-700 dark:text-zinc-400',
                                    'passed' => 'text-emerald-600 bg-emerald-50 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800/50 dark:text-emerald-400',
                                    'failed' => 'text-red-600 bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800/50 dark:text-red-400',
                                ];
                                $currentColor = $statusColors[$interview->status] ?? $statusColors['scheduled'];
                            @endphp
                            <x-custom-dropdown align="right" width="w-36">
                                <x-slot name="trigger">
                                    <div class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-md border shadow-sm outline-none transition-colors cursor-pointer {{ $currentColor }}">
                                        {{ $interview->status }}
                                        <flux:icon.chevron-down class="size-3 opacity-70" />
                                    </div>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="py-1">
                                        @foreach(['scheduled', 'completed', 'passed', 'failed'] as $st)
                                            <button type="button" 
                                                wire:click="updateInterviewStatus({{ $interview->id }}, '{{ $st }}')"
                                                class="w-full text-left px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors {{ $interview->status === $st ? 'text-brand-500 font-medium' : 'text-zinc-700 dark:text-zinc-300' }}">
                                                {{ $st }}
                                            </button>
                                        @endforeach
                                    </div>
                                </x-slot>
                            </x-custom-dropdown>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-3">
                                @if ($interview->evaluation_path)
                                    <a href="{{ Storage::url($interview->evaluation_path) }}" target="_blank"
                                        class="text-zinc-400 hover:text-brand-500 transition-colors" title="{{ __('View file') }}">
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
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <flux:button size="sm" variant="ghost" wire:click="openEdit({{ $interview->id }})"
                                    icon="pencil" />
                                
                                @if ($tab === 'hr' && $interview->status === 'passed')
                                    @php
                                        $app = $applications->firstWhere('id', $interview->application_id);
                                        $hasUserInterview = $app ? $app->interviews->contains('interview_type', 'User Interview') : false;
                                    @endphp
                                    @if (!$hasUserInterview)
                                        @if ($interview->evaluation_path)
                                            <flux:button size="sm" variant="primary" 
                                                wire:click="openScheduleUserInterview({{ $interview->application_id }})">
                                                {{ __('Jadwalkan Int. User') }}
                                            </flux:button>
                                        @else
                                            <div class="text-xs text-orange-500 w-24 leading-tight font-medium" title="{{ __('Unggah dokumen pada kolom Evaluation') }}">{{ __('Menunggu file penilaian') }}</div>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-zinc-400">{{ __('No interview data yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $interviews->links() }}</div>

    <flux:modal wire:model="showModal" class="w-full max-w-2xl">
        <div class="space-y-4">
            <flux:heading size="lg">{{ $editingId ? __('Update Interview') : __('Schedule Interview') }}</flux:heading>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Candidate & Position') }}</flux:label>
                        <div class="px-3 py-2 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-white/10 text-sm font-medium text-zinc-600 dark:text-zinc-300">
                            @php
                                $lockedApp = $applications->firstWhere('id', $application_id);
                                $lockedLabel = $lockedApp ? $lockedApp->candidate->name . ' - ' . $lockedApp->job->title : '—';
                            @endphp
                            {{ $lockedLabel }}
                        </div>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ $interview_type === 'HR Interview' ? __('Interviewer (HR)') : __('Interviewer (User)') }}</flux:label>
                        <x-custom-select wire:model="interviewer_id" :options="['' => __('Select interviewer')] + $interviewers->mapWithKeys(fn($u) => [$u->id => $u->name])->toArray()" :searchable="true" />
                        <flux:error name="interviewer_id" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Interview Type') }}</flux:label>
                        <div class="px-3 py-2 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-white/10 text-sm font-medium text-zinc-600 dark:text-zinc-300">
                            {{ $interview_type }}
                        </div>
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('Status') }}</flux:label>
                        <x-custom-select wire:model="status" :options="['scheduled' => 'scheduled', 'completed' => 'completed', 'passed' => 'passed', 'failed' => 'failed']" />
                        <flux:error name="status" />
                    </flux:field>
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

                <flux:field>
                    <flux:label>{{ __('Evaluation File (PDF/DOCX)') }}</flux:label>
                    <input type="file" wire:model="evaluation_file" class="block w-full text-sm text-zinc-600 dark:text-zinc-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-300 dark:hover:file:bg-zinc-700 focus:outline-none cursor-pointer" />
                    <flux:error name="evaluation_file" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Catatan Interview') }}</flux:label>
                    <flux:textarea rows="4" wire:model="hr_notes" />
                    <flux:error name="hr_notes" />
                </flux:field>

                <div class="flex justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="$set('showModal', false)">{{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- Evaluation Upload Modal --}}
    <flux:modal wire:model="showUploadModal" class="w-full max-w-md">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Upload Penilaian Interview') }}</flux:heading>
            <flux:subheading>{{ __('Silakan unggah dokumen hasil wawancara (PDF/DOCX).') }}</flux:subheading>

            <form wire:submit="saveUpload" class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Dokumen Penilaian') }}</flux:label>
                    <input type="file" wire:model="upload_file" class="block w-full text-sm text-zinc-600 dark:text-zinc-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-300 dark:hover:file:bg-zinc-700 focus:outline-none cursor-pointer" />
                    <flux:error name="upload_file" />
                </flux:field>

                <div class="flex justify-end gap-3 mt-4">
                    <flux:button type="button" variant="ghost" wire:click="$set('showUploadModal', false)">{{ __('Batal') }}</flux:button>
                    <flux:button type="submit" variant="primary">{{ __('Upload') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>