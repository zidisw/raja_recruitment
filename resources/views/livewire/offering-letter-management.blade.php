<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Offering Letter') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Manage offering letter responses') }}</flux:subheading>
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
                <flux:label>{{ __('Offer Status') }}</flux:label>
                <x-custom-select wire:model.live="filterStatus" :options="[
        '' => __('All status'),
        'none' => __('No offering yet'),
        'waiting_response' => __('Waiting Response'),
        'signed' => __('Signed by Candidate'),
        'accepted' => __('Accepted / Validated'),
        'rejected' => __('Rejected'),
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
                        <th>{{ __('Candidate Name') }}</th>
                        <th>{{ __('Position') }}</th>
                        <th class="text-center!">{{ __('Offer Date') }}</th>
                        <th class="text-center!">{{ __('Status') }}</th>
                        <th class="text-center! whitespace-nowrap w-px">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @forelse ($applications_paginated as $app)
                        <tr wire:key="offering-row-{{ $app->id }}">
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
                            <td class="px-6 py-4 text-center">
                                {{ $app->offeringLetter?->offer_date?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($app->offeringLetter)
                                    @php
                                        $statusColors = [
                                            'waiting_response' => 'text-amber-600 bg-amber-50 border-amber-200 dark:bg-amber-900/20 dark:border-amber-800/50 dark:text-amber-400',
                                            'signed' => 'text-blue-600 bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800/50 dark:text-blue-400',
                                            'accepted' => 'text-emerald-600 bg-emerald-50 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800/50 dark:text-emerald-400',
                                            'rejected' => 'text-red-600 bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800/50 dark:text-red-400',
                                        ];
                                        $currentColor = $statusColors[$app->offeringLetter->status] ?? $statusColors['waiting_response'];
                                    @endphp
                                    <div
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-md border shadow-sm {{ $currentColor }}">
                                        {{ \App\Enums\OfferingStatus::from($app->offeringLetter->status)->label() }}
                                    </div>
                                @else
                                    <span
                                        class="text-zinc-400 text-xs font-semibold px-2 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-md">{{ __('Waiting') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap w-px">
                                <div class="inline-flex flex-nowrap items-center justify-center gap-2 whitespace-nowrap">
                                    @if($app->offeringLetter)
                                        @if($app->offeringLetter->file_path)
                                            <a href="{{ Storage::url($app->offeringLetter->file_path) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1 text-sm text-zinc-500 hover:text-brand-500 transition-colors"
                                               title="{{ __('Lihat File OL') }}">
                                                <flux:icon.document-text class="size-4" />
                                                {{ __('File OL Admin') }}
                                            </a>
                                        @endif
                                        @if($app->offeringLetter->signed_file_path)
                                            <a href="{{ Storage::url($app->offeringLetter->signed_file_path) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1 text-sm text-emerald-600 hover:text-emerald-700 transition-colors"
                                                title="{{ __('Lihat OL Tertandatangan') }}">
                                                <flux:icon.document-text class="size-4" />
                                                {{ __('File Signed') }}
                                            </a>
                                        @endif

                                        <flux:button size="sm" variant="ghost"
                                            wire:click="openEdit({{ $app->offeringLetter->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="openEdit({{ $app->offeringLetter->id }})" icon="pencil"
                                            class="app-action-btn" title="{{ __('Edit Offering') }}">{{ __('Edit') }}
                                        </flux:button>
                                    @else
                                        <flux:button size="sm" variant="primary" wire:click="openCreate({{ $app->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="openCreate({{ $app->id }})">
                                            {{ __('Create Offer') }}
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @if ($expandedRow === $app->id)
                            <tr wire:key="offering-candidate-{{ $app->id }}-expanded" wire:transition.opacity.duration.200ms
                                class="bg-zinc-50/50 dark:bg-zinc-800/30">
                                <td colspan="7" class="px-6 py-4">
                                    <x-candidate-expanded-row :application="$app" />
                                    @if($app->offeringLetter)
                                        <div class="mt-4 grid gap-3 border-t border-zinc-200 pt-4 text-sm dark:border-zinc-700 sm:grid-cols-2">
                                            <div>
                                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                                                    {{ __('File Offering dari Admin') }}
                                                </p>
                                                @if($app->offeringLetter->file_path)
                                                    <a href="{{ Storage::url($app->offeringLetter->file_path) }}" target="_blank"
                                                        class="mt-1 inline-flex items-center gap-1 text-brand-500 hover:underline">
                                                        <flux:icon.document-text class="size-4" />
                                                        {{ __('Buka file OL awal') }}
                                                    </a>
                                                @else
                                                    <p class="mt-1 text-zinc-400">{{ __('Belum ada file') }}</p>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                                                    {{ __('File OL Tertandatangan Kandidat') }}
                                                </p>
                                                @if($app->offeringLetter->signed_file_path)
                                                    <a href="{{ Storage::url($app->offeringLetter->signed_file_path) }}" target="_blank"
                                                        class="mt-1 inline-flex items-center gap-1 text-emerald-600 hover:underline">
                                                        <flux:icon.document-text class="size-4" />
                                                        {{ __('Buka file signed') }}
                                                    </a>
                                                    <p class="mt-1 text-xs text-zinc-400">
                                                        {{ __('Diunggah:') }} {{ $app->offeringLetter->signed_at?->format('d M Y H:i') ?? '-' }}
                                                    </p>
                                                @else
                                                    <p class="mt-1 text-zinc-400">{{ __('Belum diunggah kandidat') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr wire:key="offering-empty">
                            <td colspan="7" class="px-6 py-8 text-center text-zinc-400">
                                {{ __('No candidates in offering stage yet.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
                    <x-custom-select wire:model="status" :options="collect($statuses)->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()" />
                    <flux:error name="status" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('File Offering (PDF)') }}</flux:label>
                    <input type="file" wire:model="offer_file" wire:key="offering-file-{{ $editingId ?? 'new' }}" accept=".pdf"
                        class="block w-full text-sm text-zinc-600 dark:text-zinc-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-300 dark:hover:file:bg-zinc-700 focus:outline-none cursor-pointer" />
                    <div wire:loading wire:target="offer_file" class="mt-2 text-sm text-brand-500">
                        {{ __('Uploading...') }}
                    </div>
                    <flux:error name="offer_file" />
                    @if($editingId && $app_offering = $this->currentOfferingLetter)
                        @if($app_offering->file_path)
                            <div class="mt-2 text-xs">
                                <a href="{{ Storage::url($app_offering->file_path) }}" target="_blank"
                                    class="text-brand-500 hover:underline inline-flex items-center gap-1">
                                    <flux:icon.document-text class="size-3" />
                                    {{ __('Lihat file OL awal dari admin') }}
                                </a>
                            </div>
                        @endif
                        @if($app_offering->signed_file_path)
                            <div class="mt-2 text-xs">
                                <a href="{{ Storage::url($app_offering->signed_file_path) }}" target="_blank"
                                    class="text-emerald-600 hover:underline inline-flex items-center gap-1">
                                    <flux:icon.document-text class="size-3" />
                                    {{ __('Lihat file OL tertandatangan kandidat') }}
                                </a>
                            </div>
                        @endif
                    @endif
                </flux:field>
                <div class="flex justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="$set('showModal', false)">{{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                        wire:target="save,offer_file">
                        <span wire:loading.remove wire:target="save">{{ __('Save') }}</span>
                        <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
