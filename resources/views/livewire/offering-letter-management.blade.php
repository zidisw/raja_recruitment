<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Offering Letter') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Manage offering letter responses') }}</flux:subheading>
        </div>
        <flux:button wire:click="openCreate" variant="primary" icon="plus">{{ __('New Offering') }}</flux:button>
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
                    <th>{{ __('Candidate Name') }}</th>
                    <th>{{ __('Position') }}</th>
                    <th class="text-center!">{{ __('Offer Date') }}</th>
                    <th class="text-center!">{{ __('Status') }}</th>
                    <th class="text-center!">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($applications_paginated as $app)
                    <tr>
                        <td class="px-6 py-4 font-semibold">{{ $app->candidate->name }}</td>
                        <td class="px-6 py-4">{{ $app->job->title }}</td>
                        <td class="px-6 py-4 text-center">{{ $app->offeringLetter?->offer_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($app->offeringLetter)
                                @php
                                    $statusColors = [
                                        'waiting_response' => 'text-amber-600 bg-amber-50 border-amber-200 dark:bg-amber-900/20 dark:border-amber-800/50 dark:text-amber-400',
                                        'accepted' => 'text-emerald-600 bg-emerald-50 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800/50 dark:text-emerald-400',
                                        'rejected' => 'text-red-600 bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800/50 dark:text-red-400',
                                    ];
                                    $currentColor = $statusColors[$app->offeringLetter->status] ?? $statusColors['waiting_response'];
                                @endphp
                                <div class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-md border shadow-sm {{ $currentColor }}">
                                    {{ str_replace('_', ' ', $app->offeringLetter->status) }}
                                </div>
                            @else
                                <span class="text-zinc-400 text-xs font-semibold px-2 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-md">{{ __('Waiting') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($app->offeringLetter)
                                <flux:button size="sm" variant="ghost" wire:click="openEdit({{ $app->offeringLetter->id }})"
                                    icon="pencil" />
                            @else
                                <flux:button size="sm" variant="ghost" wire:click="openCreate({{ $app->id }})"
                                    icon="plus" />
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-zinc-400">{{ __('No candidates in offering stage yet.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $applications_paginated->links() }}</div>

    <flux:modal wire:model="showModal" class="w-full max-w-xl">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Offering Letter') }}</flux:heading>
            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Candidate & Position') }}</flux:label>
                    <x-custom-select wire:model="application_id" :options="['' => __('Select candidate')] + $applications->mapWithKeys(fn($a) => [$a->id => $a->candidate->name . ' - ' . $a->job->title])->toArray()" :searchable="true" />
                    <flux:error name="application_id" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Offer Date') }}</flux:label>
                    <x-date-picker wire:model="offer_date" mode="date" placeholder="{{ __('Select offer date...') }}" />
                    <flux:error name="offer_date" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Status') }}</flux:label>
                    <x-custom-select wire:model="status" :options="['waiting_response' => 'waiting_response', 'accepted' => 'accepted', 'rejected' => 'rejected']" />
                    <flux:error name="status" />
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