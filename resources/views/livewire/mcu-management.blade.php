<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('MCU') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Medical check-up results') }}</flux:subheading>
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
                    <th class="w-12 text-center!">{{ __('No.') }}</th>
                    <th>{{ __('Candidate') }}</th>
                    <th>{{ __('Position') }}</th>
                    <th class="text-center!">{{ __('MCU Date') }}</th>
                    <th class="text-center!">{{ __('Result') }}</th>
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
                        <td class="px-6 py-4 font-semibold">{{ $app->candidate->name }}</td>
                        <td class="px-6 py-4">{{ $app->job->title }}</td>
                        <td class="px-6 py-4 text-center">{{ $app->mcu?->mcu_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($app->mcu)
                                <flux:badge size="sm" variant="outline">{{ $app->mcu->result }}</flux:badge>
                            @else
                                <span class="text-zinc-400 text-xs font-semibold px-2 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-md">{{ __('Waiting') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $app->mcu?->notes ?: '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($app->mcu)
                                <flux:button size="sm" variant="ghost" wire:click="openEdit({{ $app->mcu->id }})" wire:target="openEdit({{ $app->mcu->id }})"
                                    icon="pencil" />
                            @else
                                <flux:button size="sm" variant="ghost" wire:click="openCreate({{ $app->id }})" wire:target="openCreate({{ $app->id }})"
                                    icon="plus" />
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-zinc-400">{{ __('No candidates in MCU stage yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $applications_paginated->links() }}</div>

    <flux:modal wire:model="showModal" class="w-full max-w-xl">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('MCU Result') }}</flux:heading>
            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Candidate & Position') }}</flux:label>
                    <div class="px-3 py-2 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-white/10 text-sm font-medium text-zinc-600 dark:text-zinc-300">
                        @php
                            $lockedApp = $this->lockedApplication;
                            $lockedLabel = $lockedApp ? $lockedApp->candidate->name . ' - ' . $lockedApp->job->title : '—';
                        @endphp
                        {{ $lockedLabel }}
                    </div>
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('MCU Date') }}</flux:label>
                    <x-date-picker wire:model="mcu_date" mode="date" placeholder="{{ __('Select MCU date...') }}" />
                    <flux:error name="mcu_date" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Result') }}</flux:label><x-custom-select wire:model="result" :options="['fit' => 'fit', 'unfit' => 'unfit']" />
                    <flux:error name="result" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('MCU Document (PDF)') }}</flux:label>
                    <div class="space-y-3">
                        @if($editingId && $app_mcu = $this->currentMcu)
                            @if($app_mcu->file_path)
                                <a href="{{ Storage::url($app_mcu->file_path) }}" target="_blank" class="text-brand-500 hover:underline inline-flex items-center gap-1">
                                    <flux:icon.document-text class="size-4"/> {{ __('View Current Document') }}
                                </a>
                            @endif
                        @endif
                        <flux:input type="file" wire:model="mcu_file" accept=".pdf" />
                        <div wire:loading wire:target="mcu_file" class="text-sm text-brand-500">{{ __('Uploading...') }}</div>
                    </div>
                    <flux:error name="mcu_file" />
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