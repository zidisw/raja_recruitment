<div class="flex flex-col gap-8">
    <div>
        <flux:heading size="xl" level="1">{{ __('Applications') }}</flux:heading>
        <flux:subheading size="lg">{{ __('Select a job to view its applicants') }}</flux:subheading>
    </div>

    <flux:separator variant="subtle" />

    {{-- Filters --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
        <flux:field class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search job title...') }}"
                icon="magnifying-glass" />
        </flux:field>
        @if (!$isHR)
            <div class="min-w-40">
                <x-custom-select wire:model.live="filterDepartment" placeholder="{{ __('All Departments') }}"
                    :options="['' => __('All Departments')] + $departments->pluck('name', 'id')->toArray()" />
            </div>
        @endif
        <div class="min-w-36">
            <x-custom-select wire:model.live="filterSite" placeholder="{{ __('All Sites') }}" :options="['' => __('All Sites')] + $sites->pluck('name', 'id')->toArray()" />
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-zinc-500">{{ __('Per page:') }}</span>
            <div class="w-20">
                <x-custom-select wire:model.live="perPage" placeholder="10" :options="['10' => '10', '30' => '30', '50' => '50', '100' => '100']" />
            </div>
        </div>
    </div>

    @if ($jobs->isEmpty())
        <div
            class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-zinc-200 bg-zinc-50 p-16 dark:border-zinc-700 dark:bg-zinc-900/50">
            <flux:icon.briefcase class="mb-6 h-16 w-16 text-zinc-300 dark:text-zinc-600" />
            <flux:heading size="lg" class="mb-2">{{ __('No Job Postings Found') }}</flux:heading>
            <flux:text class="max-w-md text-center">{{ __('No job postings match your search filters.') }}</flux:text>
        </div>
    @else
        <div class="glass-card-static overflow-hidden p-0!">
            <table class="w-full text-sm modern-table">
                <thead>
                    <tr>
                        <th>{{ __('Job Title') }}</th>
                        <th class="hidden md:table-cell">{{ __('Department') }}</th>
                        <th class="hidden lg:table-cell">{{ __('Site') }}</th>
                        <th class="text-center!">{{ __('Applicants') }}</th>
                        <th class="text-center!">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-800 dark:bg-zinc-900">
                    @foreach ($jobs as $job)
                        <tr wire:key="{{ $job->id }}" class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-zinc-900 dark:text-white">{{ $job->title }}</div>
                                <div class="text-xs text-zinc-400">{{ $job->is_active ? __('Active') : __('Closed') }}</div>
                            </td>
                            <td class="hidden px-6 py-4 text-zinc-500 dark:text-zinc-400 md:table-cell">
                                {{ $job->department?->name ?? '—' }}
                            </td>
                            <td class="hidden px-6 py-4 text-zinc-500 dark:text-zinc-400 lg:table-cell">
                                {{ $job->site?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:badge variant="outline">{{ $job->applications_count }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:button href="{{ route('applications.job', $job) }}" wire:navigate size="sm"
                                    variant="ghost" icon="users">
                                    {{ __('View Applicants') }}
                                </flux:button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>{{ $jobs->links() }}</div>
    @endif
</div>