<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Psychotest') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Psychotest results and notes') }}</flux:subheading>
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
                <flux:label>{{ __('Result') }}</flux:label>
                <x-custom-select wire:model.live="filterResult" :options="[
        '' => __('All result'),
        'none' => __('No result yet'),
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
        <table class="w-full text-sm modern-table">
            <thead>
                <tr>
                    <th class="w-12 text-center!">{{ __('No.') }}</th>
                    <th class="w-12"></th>
                    <th>{{ __('Candidate') }}</th>
                    <th>{{ __('Position') }}</th>
                    <th class="text-center!">{{ __('Test Date') }}</th>
                    <th class="text-center!">{{ __('Result') }}</th>
                    <th class="text-center!">{{ __('Document') }}</th>
                    <th>{{ __('Notes') }}</th>
                    <th class="text-center!">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($applications_paginated as $app)
                    <tr>
                        <td class="px-4 py-3 text-center text-zinc-500 font-medium">
                            {{ ($applications_paginated->currentPage() - 1) * $applications_paginated->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-4 py-3">
                            @php $isExpanded = $expandedRow === $app->id; @endphp
                            <button wire:click="toggleExpand({{ $app->id }})" type="button"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-400 transition-all duration-200 hover:bg-zinc-100 dark:hover:bg-zinc-700/70 hover:text-zinc-600 dark:hover:text-zinc-300 active:scale-95"
                                aria-label="{{ $isExpanded ? __('Collapse details') : __('Expand details') }}">
                                <flux:icon.chevron-right
                                    class="size-4 transition-transform duration-300 ease-out {{ $isExpanded ? 'rotate-90' : '' }}" />
                            </button>
                        </td>
                        <td class="px-6 py-4 font-semibold">{{ $app->candidate->name }}</td>
                        <td class="px-6 py-4">{{ $app->job->title }}</td>
                        <td class="px-6 py-4 text-center">{{ $app->psychotest?->test_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($app->psychotest)
                                <flux:badge size="sm" variant="outline">{{ ucfirst($app->psychotest->result) }}</flux:badge>
                            @else
                                <span
                                    class="text-zinc-400 text-xs font-semibold px-2 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-md">{{ __('Waiting') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($app->psychotest?->file_path)
                                <a href="{{ Storage::url($app->psychotest->file_path) }}" target="_blank"
                                    class="inline-flex items-center justify-center text-zinc-400 hover:text-brand-500"
                                    title="{{ __('View document') }}">
                                    <flux:icon.document-text class="size-5" />
                                </a>
                            @else
                                <span class="text-zinc-300 dark:text-zinc-600">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $app->psychotest?->notes ?: '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($app->psychotest)
                                <div class="flex items-center justify-center gap-2">
                                    <flux:button size="sm" variant="ghost" wire:click="openEdit({{ $app->psychotest->id }})"
                                        wire:target="openEdit({{ $app->psychotest->id }})" icon="pencil" />
                                </div>
                            @else
                                <flux:button size="sm" variant="ghost" wire:click="openCreate({{ $app->id }})"
                                    wire:target="openCreate({{ $app->id }})" icon="plus" />
                            @endif
                        </td>
                    </tr>
                    @if ($expandedRow === $app->id)
                        <tr wire:key="psychotest-candidate-{{ $app->id }}-expanded" wire:transition.opacity.duration.200ms
                            class="bg-zinc-50/50 dark:bg-zinc-800/30">
                            <td colspan="9" class="px-6 py-4">
                                <x-candidate-expanded-row :application="$app" />
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-zinc-400">
                            {{ __('No candidates in Psychotest stage yet.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $applications_paginated->links() }}</div>

    <flux:modal wire:model="showModal" class="w-full max-w-xl">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Psychotest Result') }}</flux:heading>
            <form wire:submit="save" class="space-y-4">
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
                    <flux:label>{{ __('Test Date') }}</flux:label>
                    <x-date-picker wire:model="test_date" mode="date" placeholder="{{ __('Select test date...') }}" />
                    <flux:error name="test_date" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Result') }}</flux:label><x-custom-select wire:model="result"
                        :options="['passed' => 'Passed', 'failed' => 'Failed']" />
                    <flux:error name="result" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Psychotest Document (PDF)') }}</flux:label>
                    <div class="space-y-3">
                        @if($editingId && $app_psychotest = $this->currentPsychotest)
                            @if($app_psychotest->file_path)
                                <a href="{{ Storage::url($app_psychotest->file_path) }}" target="_blank"
                                    class="text-brand-500 hover:underline inline-flex items-center gap-1">
                                    <flux:icon.document-text class="size-4" /> {{ __('View Current Document') }}
                                </a>
                            @endif
                        @endif
                        <flux:input type="file" wire:model="psychotest_file" accept=".pdf" />
                        <div wire:loading wire:target="psychotest_file" class="text-sm text-brand-500">
                            {{ __('Uploading...') }}
                        </div>
                    </div>
                    <flux:error name="psychotest_file" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Notes') }}</flux:label>
                    <flux:textarea rows="3" wire:model="notes" />
                    <flux:error name="notes" />
                </flux:field>
                <div class="flex justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="$set('showModal', false)">{{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>