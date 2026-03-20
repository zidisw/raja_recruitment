<div class="flex flex-col gap-8">
    {{-- Futuristic Welcome Card --}}
    <div class="glass-card-static relative overflow-hidden">
        <div
            class="absolute top-0 right-0 size-32 rounded-full bg-linear-to-br from-brand-500/20 to-brand-400/10 blur-3xl translate-x-1/2 -translate-y-1/2">
        </div>
        <div
            class="absolute bottom-0 left-0 size-24 rounded-full bg-linear-to-tr from-blue-500/10 to-purple-500/10 blur-2xl -translate-x-1/2 translate-y-1/2">
        </div>
        <div class="relative">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                {{ __('Welcome back,') }}
                <span
                    class="bg-linear-to-r from-brand-500 to-brand-400 bg-clip-text text-transparent">{{ auth()->user()->name }}</span>
                👋
            </h1>
            <p class="mt-1.5 text-sm text-zinc-500 dark:text-zinc-400">
                {{ now()->translatedFormat('l, d F Y') }} ·
                {{ __(':role Dashboard', ['role' => auth()->user()->role->label()]) }}
            </p>
        </div>
    </div>

    <flux:separator variant="subtle" />

    @php $user = auth()->user(); @endphp

    {{-- Superadmin Dashboard --}}
    @if ($user->isSuperAdmin())
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-brand-500/10">
                        <flux:icon.document-text class="size-5 text-brand-500" />
                    </div>
                    <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Applications') }}
                    </flux:text>
                </div>
                <p class="mt-3 text-3xl font-bold text-zinc-900 dark:text-white">{{ $stats['total_applications'] }}</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-blue-500/10">
                        <flux:icon.briefcase class="size-5 text-blue-500" />
                    </div>
                    <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Active Jobs') }}
                    </flux:text>
                </div>
                <p class="mt-3 text-3xl font-bold text-zinc-900 dark:text-white">{{ $stats['active_jobs'] }}</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-purple-500/10">
                        <flux:icon.users class="size-5 text-purple-500" />
                    </div>
                    <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Candidates') }}
                    </flux:text>
                </div>
                <p class="mt-3 text-3xl font-bold text-zinc-900 dark:text-white">{{ $stats['total_candidates'] }}</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-green-500/10">
                        <flux:icon.check-badge class="size-5 text-green-500" />
                    </div>
                    <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Hired This Month') }}
                    </flux:text>
                </div>
                <p class="mt-3 text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['hired_this_month'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            @if (!empty($stats['funnel_chart']))
                <div
                    class="rounded-2xl border border-zinc-200 bg-white/70 backdrop-blur-xl p-6 shadow-xl dark:border-white/10 dark:bg-zinc-900/70">
                    <flux:heading size="md" class="mb-5">{{ __('Recruitment Funnel') }}</flux:heading>
                    <div id="funnel-chart" class="w-full" style="min-height: 250px;"></div>
                </div>
            @endif

            @if (!empty($stats['monthly_trend']))
                <div
                    class="rounded-2xl border border-zinc-200 bg-white/70 backdrop-blur-xl p-6 shadow-xl dark:border-white/10 dark:bg-zinc-900/70">
                    <flux:heading size="md" class="mb-5">{{ __('Applicants This Year') }}</flux:heading>
                    <div id="trend-chart" class="w-full" style="min-height: 250px;"></div>
                </div>
            @endif
        </div>

        <script id="dashboard-chart-data" type="application/json">
                {
                    "funnelData": {!! json_encode($stats['funnel_chart'] ?? []) !!},
                    "trendData": {!! json_encode(array_values($stats['monthly_trend'] ?? [])) !!},
                    "trendLabels": {!! json_encode(array_keys($stats['monthly_trend'] ?? [])) !!}
                }
            </script>

        @script
        <script>
            // Run directly so charts initialize on every component mount (including navigate-back).
            // Using livewire:initialized would skip re-init because that event only fires once.
            (() => {
                const chartData = JSON.parse(document.getElementById('dashboard-chart-data').textContent);
                const funnelData = chartData.funnelData;
                const trendData = chartData.trendData;
                const trendLabels = chartData.trendLabels;

                const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                const mappedLabels = trendLabels.map(m => monthNames[m - 1]);

                if (document.querySelector("#funnel-chart") && funnelData.length > 0) {
                    new ApexCharts(document.querySelector("#funnel-chart"), {
                        series: [{
                            name: 'Applicants',
                            data: funnelData.map(item => item.data[0])
                        }],
                        chart: { type: 'bar', height: 250, toolbar: { show: false }, background: 'transparent' },
                        plotOptions: {
                            bar: { borderRadius: 4, horizontal: true, distributed: true, }
                        },
                        colors: ['#3b82f6', '#8b5cf6', '#f59e0b', '#10b981', '#ec4899', '#6366f1'],
                        dataLabels: { enabled: true, style: { fontSize: '12px' } },
                        xaxis: { categories: funnelData.map(item => item.name) },
                        legend: { show: false }
                    }).render();
                }

                if (document.querySelector("#trend-chart") && trendData.length > 0) {
                    new ApexCharts(document.querySelector("#trend-chart"), {
                        series: [{ name: 'Applicants', data: trendData }],
                        chart: { type: 'area', height: 250, toolbar: { show: false }, background: 'transparent' },
                        colors: ['#f5a623'],
                        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.2, stops: [0, 90, 100] } },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 2 },
                        xaxis: { categories: mappedLabels }
                    }).render();
                }
            })();
        </script>
        @endscript
    @endif

    {{-- Admin Dashboard --}}
    @if ($user->hasAdminRole())
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-brand-500/10">
                        <flux:icon.briefcase class="size-5 text-brand-500" />
                    </div>
                    <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Open Jobs') }}
                    </flux:text>
                </div>
                <p class="mt-3 text-3xl font-bold text-zinc-900 dark:text-white">{{ $stats['total_open_jobs'] }}</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-blue-500/10">
                        <flux:icon.document-text class="size-5 text-blue-500" />
                    </div>
                    <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Applicants') }}
                    </flux:text>
                </div>
                <p class="mt-3 text-3xl font-bold text-zinc-900 dark:text-white">{{ $stats['total_applicants'] }}</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-amber-500/10">
                        <flux:icon.clock class="size-5 text-amber-500" />
                    </div>
                    <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Administrative Passed') }}
                    </flux:text>
                </div>
                <p class="mt-3 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['administrative_passed'] }}</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-green-500/10">
                        <flux:icon.calendar class="size-5 text-green-500" />
                    </div>
                    <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Interview Scheduled') }}
                    </flux:text>
                </div>
                <p class="mt-3 text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['interview_scheduled'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-4">
            <div class="stat-card">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-indigo-500/10">
                        <flux:icon.document-check class="size-5 text-indigo-500" />
                    </div>
                    <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Offering Sent') }}</flux:text>
                </div>
                <p class="mt-3 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['offering_sent'] }}</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-emerald-500/10">
                        <flux:icon.check-badge class="size-5 text-emerald-500" />
                    </div>
                    <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Hired Candidates') }}</flux:text>
                </div>
                <p class="mt-3 text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['hired_candidates'] }}</p>
            </div>
        </div>

        <div class="glass-card-static mt-6">
            <flux:heading size="md" class="mb-4">{{ __('Pipeline Chart') }}</flux:heading>
            @php
                $pipeline = $stats['pipeline'] ?? ['applied' => 0, 'interview' => 0, 'offer' => 0, 'hired' => 0];
                $maxVal = max(1, $pipeline['applied'], $pipeline['interview'], $pipeline['offer'], $pipeline['hired']);
            @endphp
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-sm mb-1"><span>{{ __('Applied') }}</span><span>{{ $pipeline['applied'] }}</span></div>
                    <div class="h-2 rounded bg-zinc-100 dark:bg-zinc-800"><div class="h-2 rounded bg-sky-500" style="width: {{ ($pipeline['applied'] / $maxVal) * 100 }}%"></div></div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1"><span>{{ __('Interview') }}</span><span>{{ $pipeline['interview'] }}</span></div>
                    <div class="h-2 rounded bg-zinc-100 dark:bg-zinc-800"><div class="h-2 rounded bg-amber-500" style="width: {{ ($pipeline['interview'] / $maxVal) * 100 }}%"></div></div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1"><span>{{ __('Offer') }}</span><span>{{ $pipeline['offer'] }}</span></div>
                    <div class="h-2 rounded bg-zinc-100 dark:bg-zinc-800"><div class="h-2 rounded bg-indigo-500" style="width: {{ ($pipeline['offer'] / $maxVal) * 100 }}%"></div></div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1"><span>{{ __('Hired') }}</span><span>{{ $pipeline['hired'] }}</span></div>
                    <div class="h-2 rounded bg-zinc-100 dark:bg-zinc-800"><div class="h-2 rounded bg-emerald-500" style="width: {{ ($pipeline['hired'] / $maxVal) * 100 }}%"></div></div>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <flux:button variant="primary" href="{{ route('applications.index') }}" wire:navigate icon="document-text">
                {{ __('Review Applications') }}
            </flux:button>
            <flux:button variant="ghost" href="{{ route('jobs.index') }}" wire:navigate icon="briefcase">
                {{ __('Manage Jobs') }}
            </flux:button>
        </div>
    @endif

</div>