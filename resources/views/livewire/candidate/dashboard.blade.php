<div class="flex flex-col gap-8">
    <div class="glass-card-static relative overflow-hidden">
        <div
            class="absolute -right-8 -top-8 size-32 rounded-full bg-linear-to-br from-brand-500/20 to-brand-400/10 blur-2xl">
        </div>
        <div class="relative">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                {{ __('Candidate Dashboard') }}
            </h1>
            <p class="mt-1.5 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Overview of your applications and available opportunities') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card">
            <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Open Jobs') }}</flux:text>
            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">{{ $stats['open_jobs'] }}</p>
        </div>
        <div class="stat-card">
            <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('My Applications') }}
            </flux:text>
            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">{{ $stats['my_total'] }}</p>
        </div>
        <div class="stat-card">
            <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Ongoing') }}</flux:text>
            <p class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['my_ongoing'] }}</p>
        </div>
        <div class="stat-card">
            <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Slots Left') }}</flux:text>
            <p class="mt-2 text-3xl font-bold text-brand-600 dark:text-brand-400">{{ $stats['remaining_slots'] }}/2</p>
        </div>
    </div>

    <div class="glass-card-static">
        <div class="mb-4 flex items-center justify-between">
            <flux:heading size="md">{{ __('Quick Actions') }}</flux:heading>
        </div>
        <div class="flex flex-wrap gap-2">
            <flux:button href="{{ route('candidate.portal') }}" wire:navigate variant="primary" icon="briefcase">
                {{ __('Browse Jobs') }}
            </flux:button>
            <flux:button href="{{ route('candidate.applications') }}" wire:navigate variant="ghost" icon="chart-bar">
                {{ __('Track Applications') }}
            </flux:button>
            <flux:button href="{{ route('candidate.profile') }}" wire:navigate variant="ghost" icon="user">
                {{ __('Update Profile') }}
            </flux:button>
        </div>
    </div>

    <div class="glass-card-static p-0!">
        <div class="overflow-x-auto">
            <table class="w-full min-w-140 text-sm modern-table">
                <thead>
                    <tr>
                        <th>{{ __('Latest Applications') }}</th>
                        <th class="text-center!">{{ __('Applied At') }}</th>
                        <th class="text-center!">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-800 dark:bg-zinc-900">
                    @forelse ($latestApplications as $application)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-zinc-900 dark:text-white">{{ $application->job->title }}</p>
                            </td>
                            <td class="px-6 py-4 text-center text-zinc-500 dark:text-zinc-400">
                                {{ $application->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:badge variant="outline" size="sm">{{ $application->recruitment_stage->label() }}</flux:badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-zinc-400">{{ __('No applications yet.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>