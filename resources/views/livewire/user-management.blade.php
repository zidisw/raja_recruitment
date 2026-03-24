<div class="flex flex-col gap-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('User Management') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Manage system access, roles, and department assignments') }}
            </flux:subheading>
        </div>

        <flux:button wire:click="openCreate" variant="primary" icon="plus" class="w-full md:w-auto">
            {{ __('Add User') }}
        </flux:button>
    </div>

    <flux:separator variant="subtle" />

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>{{ session('success') }}</flux:callout.heading>
        </flux:callout>
    @endif

    <div class="flex items-center gap-3">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="{{ __('Search users...') }}"
            class="max-w-sm" />
    </div>

    @if ($users->isEmpty())
        <div
            class="flex flex-col items-center justify-center p-16 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
            <flux:icon.users class="w-16 h-16 text-zinc-300 dark:text-zinc-600 mb-6" />
            <flux:heading size="lg" class="mb-2">{{ __('No Users Found') }}</flux:heading>
            <flux:text class="text-center max-w-md">
                {{ __('No users matched your search.') }}
            </flux:text>
        </div>
    @else
        <div class="glass-card-static overflow-hidden p-0">
            <table class="w-full text-sm modern-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center!">{{ __('No.') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th class="hidden md:table-cell">{{ __('Email') }}</th>
                        <th class="text-center!">{{ __('Role') }}</th>
                        <th class="hidden lg:table-cell">{{ __('Department') }}</th>
                        <th class="text-center! hidden sm:table-cell">{{ __('Verified') }}</th>
                        <th class="text-center!">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @foreach ($users as $user)
                        <tr wire:key="{{ $user->id }}" class="cursor-pointer">
                            <td class="px-4 py-3 text-center text-zinc-500 font-medium whitespace-nowrap">
                                {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <flux:avatar :name="$user->name" :initials="$user->initials()" size="sm" />
                                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400 hidden md:table-cell">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $normalizedRole = $user->role->normalized()->value;
                                    $roleColors = [
                                        'user' => 'zinc',
                                        'admin' => 'blue',
                                        'super_admin' => 'purple',
                                    ];
                                @endphp
                                <flux:badge color="{{ $roleColors[$normalizedRole] ?? 'zinc' }}" size="sm">
                                    {{ $user->role->label() }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400 hidden lg:table-cell">
                                {{ $user->department?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-center hidden sm:table-cell">
                                @if ($user->email_verified_at)
                                    <flux:icon.check-circle class="w-5 h-5 text-green-500 inline-block" />
                                @else
                                    <flux:icon.x-circle class="w-5 h-5 text-zinc-400 inline-block" />
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:button wire:click="openEdit({{ $user->id }})" wire:target="openEdit({{ $user->id }})" size="sm" variant="ghost" icon="pencil" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800">
                {{ $users->links() }}
            </div>
        @endif
    @endif

    <flux:modal wire:model="showModal" class="w-full max-w-lg">
        <div class="space-y-6">
            <flux:heading size="lg">{{ $editingId ? __('Edit User') : __('New User') }}</flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Name') }} *</flux:label>
                    <flux:input wire:model="name" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Email') }} *</flux:label>
                    <flux:input wire:model="email" type="email" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Role') }} *</flux:label>
                    <x-custom-select wire:model.live="role" placeholder="{{ __('Select role...') }}" :options="['' => __('Select role...')] + collect($roles)->mapWithKeys(fn($r) => [$r->value => __($r->label())])->toArray()" />
                    <flux:error name="role" />
                </flux:field>

                @if ($role === 'admin')
                    <flux:field>
                        <flux:label>{{ __('Department') }}</flux:label>
                        <x-custom-select wire:model="department_id" placeholder="{{ __('No department') }}" :options="['' => __('No department')] + $departments->pluck('name', 'id')->toArray()" :searchable="true" />
                        <flux:error name="department_id" />
                    </flux:field>
                @endif

                <flux:field>
                    <flux:label>{{ __('Password') }} {{ $editingId ? '' : '*' }}</flux:label>
                    <flux:input wire:model="password" type="password"
                        placeholder="{{ $editingId ? __('Leave blank to keep current') : __('Min. 8 characters') }}" />
                    <flux:error name="password" />
                </flux:field>

                <div class="flex justify-end gap-3 pt-2">
                    <flux:button type="button" variant="ghost" wire:click="$set('showModal', false)">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? __('Save Changes') : __('Create User') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
