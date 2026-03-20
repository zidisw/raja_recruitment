<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Onboarding') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Track onboarding completion') }}</flux:subheading>
        </div>
        <flux:button wire:click="openCreate" variant="primary" icon="plus">{{ __('Add Onboarding') }}</flux:button>
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
                    <th class="text-center!">{{ __('Joining Date') }}</th>
                    <th class="text-center!">{{ __('Onboarding Status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($onboardings as $item)
                    <tr>
                        <td class="px-4 py-4 text-center text-zinc-500 font-medium">
                            {{ $onboardings->firstItem() + $loop->index }}
                        </td>
                        <td class="px-6 py-4 font-semibold">{{ $item->application->candidate->name }}</td>
                        <td class="px-6 py-4">{{ $item->application->job->title }}</td>
                        <td class="px-6 py-4 text-center">{{ $item->joining_date?->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <flux:badge size="sm" variant="outline">{{ $item->onboarding_status }}</flux:badge>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-zinc-400">{{ __('No onboarding data yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $onboardings->links() }}</div>

    <flux:modal wire:model="showModal" class="w-full max-w-xl">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Onboarding') }}</flux:heading>
            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Candidate & Position') }}</flux:label><x-custom-select
                        wire:model="application_id" :options="['' => __('Select candidate')] + $applications->mapWithKeys(fn($a) => [$a->id => $a->candidate->name . ' - ' . $a->job->title])->toArray()" :searchable="true" />
                    <flux:error name="application_id" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Joining Date') }}</flux:label><x-date-picker wire:model="joining_date"
                        mode="date" placeholder="{{ __('Select joining date...') }}" />
                    <flux:error name="joining_date" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Onboarding Status') }}</flux:label><x-custom-select
                        wire:model="onboarding_status" :options="['pending' => 'pending', 'completed' => 'completed']" />
                    <flux:error name="onboarding_status" />
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