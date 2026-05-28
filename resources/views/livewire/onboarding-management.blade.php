<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Onboarding') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Track onboarding completion') }}</flux:subheading>
        </div>
    </div>

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
                <flux:label>{{ __('Onboarding Status') }}</flux:label>
                <x-custom-select wire:model.live="filterStatus" :options="[
        '' => __('All status'),
        'none' => __('No onboarding yet'),
        'pending' => __('Pending'),
        'completed' => __('Completed'),
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
                        <th class="text-center!">{{ __('Joining Date') }}</th>
                        <th class="text-center!">{{ __('Onboarding Status') }}</th>
                        <th class="text-center!">{{ __('Travel Ticket') }}</th>
                        <th class="text-center! whitespace-nowrap w-px">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @forelse ($applicationsPaginated as $application)
                        @php
                            $item = $application->onboarding;
                        @endphp
                        <tr wire:key="onboarding-row-{{ $application->id }}">
                            <td class="px-4 py-4 text-center text-zinc-500 font-medium">
                                {{ $applicationsPaginated->firstItem() + $loop->index }}
                            </td>
                            <td class="px-4 py-3">
                                @php $isExpanded = $expandedRow === $application->id; @endphp
                                <button wire:click="toggleExpand({{ $application->id }})" type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-400 transition-all duration-200 hover:bg-zinc-100 dark:hover:bg-zinc-700/70 hover:text-zinc-600 dark:hover:text-zinc-300 active:scale-95"
                                    aria-label="{{ $isExpanded ? __('Collapse details') : __('Expand details') }}">
                                    <flux:icon.chevron-right
                                        class="size-4 transition-transform duration-300 ease-out {{ $isExpanded ? 'rotate-90' : '' }}" />
                                </button>
                            </td>
                            <td class="px-6 py-4 font-semibold">{{ $application->candidate->name }}</td>
                            <td class="px-6 py-4">{{ $application->job->title }}</td>
                            <td class="px-6 py-4 text-center">{{ $item?->joining_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-6 py-4 text-center">
                                <flux:badge size="sm" variant="outline">
                                    {{ $item ? \Illuminate\Support\Str::headline($item->onboarding_status) : __('Pending') }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item?->travel_ticket_number)
                                    <div class="inline-flex flex-col items-center leading-tight">
                                        <span
                                            class="font-medium text-zinc-700 dark:text-zinc-300">{{ $item->travel_ticket_number }}</span>
                                        <span
                                            class="text-xs text-zinc-400">{{ $item->travel_ticket_sent_at?->format('d M Y H:i') ?? __('Not sent') }}</span>
                                    </div>
                                @else
                                    <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap w-px">
                                @if($item)
                                    <flux:button size="sm" variant="ghost" wire:click="openEdit({{ $item->id }})"
                                        wire:target="openEdit({{ $item->id }})" icon="pencil" class="app-action-btn">
                                        {{ __('Edit') }}
                                    </flux:button>
                                @else
                                    <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                @endif
                            </td>
                        </tr>
                        @if ($expandedRow === $application->id)
                            <tr wire:key="onboarding-candidate-{{ $application->id }}-expanded"
                                wire:transition.opacity.duration.200ms class="bg-zinc-50/50 dark:bg-zinc-800/30">
                                <td colspan="8" class="px-6 py-4">
                                    <x-candidate-expanded-row :application="$application" />
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-zinc-400">{{ __('No onboarding data yet.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $applicationsPaginated->links() }}</div>

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
                        wire:model="onboarding_status" :options="['pending' => 'Pending', 'completed' => 'Completed']" />
                    <flux:error name="onboarding_status" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Travel Ticket Number') }}</flux:label>
                    <flux:input wire:model="travel_ticket_number" placeholder="{{ __('Input ticket number...') }}" />
                    <flux:error name="travel_ticket_number" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Travel Ticket Notes') }}</flux:label>
                    <flux:textarea rows="3" wire:model="travel_ticket_notes"
                        placeholder="{{ __('Optional notes for departure ticket...') }}" />
                    <flux:error name="travel_ticket_notes" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Onsite Scheduling Date') }} <span
                            class="text-zinc-400 font-normal text-xs">(optional)</span></flux:label>
                    <x-date-picker wire:model="onsite_date" mode="date"
                        placeholder="{{ __('Select onsite date...') }}" />
                    <flux:error name="onsite_date" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Onsite Location') }} <span
                            class="text-zinc-400 font-normal text-xs">(optional)</span></flux:label>
                    <flux:input wire:model="onsite_location" placeholder="{{ __('e.g. Jakarta, Main Office') }}" />
                    <flux:error name="onsite_location" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Onsite Notes') }} <span
                            class="text-zinc-400 font-normal text-xs">(optional)</span></flux:label>
                    <flux:textarea rows="3" wire:model="onsite_notes"
                        placeholder="{{ __('Additional information about onsite schedule...') }}" />
                    <flux:error name="onsite_notes" />
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
