<div class="flex flex-col gap-8">
    <div>
        <flux:heading size="xl" level="1">{{ __('Email Templates') }}</flux:heading>
        <flux:subheading size="lg">{{ __('Manage email templates for each application stage') }}</flux:subheading>
    </div>

    <flux:separator variant="subtle" />

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>{{ session('success') }}</flux:callout.heading>
        </flux:callout>
    @endif

    <div class="rounded-xl border border-zinc-200 p-4 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
        {{ __('Available placeholders:') }}
        <span class="ml-2 font-mono text-blue-500">{name}</span>,
        <span class="font-mono text-blue-500">{job}</span>,
        <span class="font-mono text-blue-500">{status}</span>,
        <span class="font-mono text-blue-500">{stage}</span>
    </div>

    {{-- Template Grid --}}
    <div class="glass-card-static overflow-hidden p-0!">
        <table class="w-full text-sm modern-table">
            <thead>
                <tr>
                    <th>{{ __('Stage') }}</th>
                    <th class="text-center!">{{ __('Staff Template') }}</th>
                    <th class="text-center!">{{ __('Non-Staff Template') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-800 dark:bg-zinc-900">
                @foreach ($stages as $stage)
                    @php
                        $staffTemplate = $templates[$stage->value . '_staff'] ?? null;
                        $nonStaffTemplate = $templates[$stage->value . '_non_staff'] ?? null;
                    @endphp
                    <tr wire:key="{{ $stage->value }}" class="cursor-pointer">
                        <td class="px-6 py-4 font-medium">{{ $stage->label() }}</td>
                        <td class="px-6 py-4 text-center">
                            @if ($staffTemplate)
                                <div class="flex flex-col items-center gap-1">
                                    <flux:badge color="green" size="sm">{{ __('Set') }}</flux:badge>
                                    <flux:button wire:click="openEdit({{ $stage->value }}, 'staff')" size="sm" variant="ghost"
                                        icon="pencil">
                                        {{ __('Edit') }}
                                    </flux:button>
                                </div>
                            @else
                                <flux:button wire:click="openEdit({{ $stage->value }}, 'staff')" size="sm" variant="ghost"
                                    icon="plus">
                                    {{ __('Create') }}
                                </flux:button>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($nonStaffTemplate)
                                <div class="flex flex-col items-center gap-1">
                                    <flux:badge color="green" size="sm">{{ __('Set') }}</flux:badge>
                                    <flux:button wire:click="openEdit({{ $stage->value }}, 'non_staff')" size="sm"
                                        variant="ghost" icon="pencil">
                                        {{ __('Edit') }}
                                    </flux:button>
                                </div>
                            @else
                                <flux:button wire:click="openEdit({{ $stage->value }}, 'non_staff')" size="sm" variant="ghost"
                                    icon="plus">
                                    {{ __('Create') }}
                                </flux:button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Edit Modal --}}
    <flux:modal wire:model="showModal" class="w-full max-w-2xl">
        <div class="space-y-5">
            <div>
                <flux:heading size="lg">{{ __('Edit Email Template') }}</flux:heading>
                <flux:text class="mt-1 text-sm text-zinc-500">
                    {{ $editingStageLabel }} · {{ $editingLevel === 'staff' ? 'Staff' : 'Non-Staff' }}
                </flux:text>
            </div>

            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Subject') }} *</flux:label>
                    <flux:input wire:model="subject" />
                    <flux:error name="subject" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Body') }} *</flux:label>
                    <flux:textarea wire:model="body" rows="10" class="font-mono text-sm" />
                    <flux:error name="body" />
                </flux:field>

                <div class="flex justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="$set('showModal', false)">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary" icon="check">
                        {{ __('Save Template') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
