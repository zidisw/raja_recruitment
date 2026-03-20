<div class="flex flex-col gap-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Department Management') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Manage departments and their assignments') }}</flux:subheading>
        </div>

        <flux:button wire:click="openCreate" variant="primary" icon="plus" class="w-full md:w-auto">
            {{ __('Add Department') }}
        </flux:button>
    </div>

    <flux:separator variant="subtle" />

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>{{ session('success') }}</flux:callout.heading>
        </flux:callout>
    @endif

    @if ($departments->isEmpty())
        <div
            class="flex flex-col items-center justify-center p-16 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
            <flux:icon.building-office-2 class="w-16 h-16 text-zinc-300 dark:text-zinc-600 mb-6" />
            <flux:heading size="lg" class="mb-2">{{ __('No Departments Yet') }}</flux:heading>
            <flux:text class="text-center max-w-md">
                {{ __('Create your first department to start organizing Admin and Recruitment access.') }}
            </flux:text>
        </div>
    @else
        <div class="glass-card-static overflow-hidden p-0!">
            <table class="w-full text-sm modern-table">
                <thead>
                    <tr>
                        <th>{{ __('Department Name') }}</th>
                        <th class="hidden md:table-cell">{{ __('Description') }}</th>
                        <th class="text-center!">{{ __('Members') }}</th>
                        <th class="text-center!">{{ __('Jobs') }}</th>
                        <th class="text-center!">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @foreach ($departments as $department)
                        <tr wire:key="{{ $department->id }}"
                            class="cursor-pointer">
                            <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-white">
                                {{ $department->name }}
                            </td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400 hidden md:table-cell max-w-xs truncate">
                                {{ $department->description ?: '—' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:badge variant="outline">{{ $department->users_count }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:badge variant="outline">{{ $department->jobs_count }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <flux:button wire:click="openEdit({{ $department->id }})" size="sm" variant="ghost"
                                        icon="pencil" />
                                    <flux:button
                                        @click="$dispatch('confirm-action', {
                                            title: 'Hapus Department?',
                                            description: 'Semua user dan job yang terhubung akan di-unassign. Aksi ini tidak dapat dibatalkan.',
                                            variant: 'danger',
                                            method: 'delete',
                                            args: [{{ $department->id }}]
                                        })"
                                        size="sm" variant="ghost" icon="trash" class="app-action-btn-danger" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <flux:modal wire:model="showModal" class="w-full max-w-lg">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $editingId ? __('Edit Department') : __('New Department') }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Name') }} *</flux:label>
                    <flux:input wire:model="name" placeholder="e.g. Human Resources" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Description') }}</flux:label>
                    <flux:textarea wire:model="description" rows="3"
                        placeholder="Brief description of this department..." />
                    <flux:error name="description" />
                </flux:field>

                <div class="flex justify-end gap-3 pt-2">
                    <flux:button type="button" variant="ghost" wire:click="$set('showModal', false)">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? __('Update') : __('Create') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <x-confirm-action />
</div>
