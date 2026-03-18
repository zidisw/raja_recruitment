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
                @forelse ($offerings as $offering)
                    <tr>
                        <td class="px-6 py-4 font-semibold">{{ $offering->application->candidate->name }}</td>
                        <td class="px-6 py-4">{{ $offering->application->job->title }}</td>
                        <td class="px-6 py-4 text-center">{{ $offering->offer_date?->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <flux:badge size="sm" variant="outline">{{ $offering->status }}</flux:badge>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <flux:button size="sm" variant="ghost" wire:click="openEdit({{ $offering->id }})"
                                icon="pencil" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-zinc-400">{{ __('No offering letters yet.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $offerings->links() }}</div>

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