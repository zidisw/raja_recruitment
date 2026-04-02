@props(['application'])

@php
    $profile = $application->candidate->profile;
@endphp

<div x-data="{ show: false }" x-init="requestAnimationFrame(() => show = true)" x-show="show"
    x-transition:enter="transition ease-out duration-220" x-transition:enter-start="opacity-0 -translate-y-1"
    x-transition:enter-end="opacity-100 translate-y-0"
    class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm sm:grid-cols-3 lg:grid-cols-4">
    <div>
        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Gender') }}</span>
        <p class="mt-0.5 capitalize text-zinc-700 dark:text-zinc-300">{{ $profile?->gender ?? '—' }}</p>
    </div>
    <div>
        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Date of Birth') }}</span>
        <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">
            @if ($profile?->date_of_birth)
                {{ $profile->date_of_birth->format('d/m/y') }}
                <span class="text-zinc-400">({{ $profile->date_of_birth->age }} {{ __('y.o.') }})</span>
            @else
                —
            @endif
        </p>
    </div>
    <div>
        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Religion') }}</span>
        <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">{{ $profile?->religion ?? '—' }}</p>
    </div>
    <div>
        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Marital Status') }}</span>
        <p class="mt-0.5 capitalize text-zinc-700 dark:text-zinc-300">{{ $profile?->marital_status ?? '—' }}</p>
    </div>
    <div>
        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('NIK') }}</span>
        <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">{{ $profile?->nik ?? '—' }}</p>
    </div>
    <div>
        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Education') }}</span>
        @php $latestEdu = $application->candidate->education->sortByDesc('end_year')->first(); @endphp
        <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">
            {{ $latestEdu ? $latestEdu->degree . ' — ' . $latestEdu->institution_name : '—' }}
        </p>
    </div>
    <div>
        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Work Exp.') }}</span>
        <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">
            @if ($application->candidate->experiences->isNotEmpty())
                <span class="text-green-600 dark:text-green-400">✓ {{ $application->candidate->experiences->count() }}
                    {{ __('job(s)') }}</span>
            @else
                <span class="text-zinc-400">{{ __('None') }}</span>
            @endif
        </p>
    </div>
    <div>
        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Organization') }}</span>
        <p class="mt-0.5 text-zinc-700 dark:text-zinc-300">
            @if ($application->candidate->organizations->isNotEmpty())
                <span class="text-green-600 dark:text-green-400">✓ {{ $application->candidate->organizations->count() }}
                    {{ __('org(s)') }}</span>
            @else
                <span class="text-zinc-400">{{ __('None') }}</span>
            @endif
        </p>
    </div>
    <div class="col-span-2 sm:col-span-3 lg:col-span-4">
        <span class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('Documents') }}</span>
        <div class="mt-1 flex flex-wrap gap-1.5">
            @if ($profile?->ktp_path)
                <flux:badge variant="outline" size="sm" icon="identification">{{ __('KTP') }}</flux:badge>
            @endif
            @if ($profile?->portfolio_path)
                <flux:badge variant="outline" size="sm" icon="document">{{ __('Portfolio') }}</flux:badge>
            @endif
            @if ($profile?->certificate_path)
                <flux:badge variant="outline" size="sm" icon="academic-cap">{{ __('Certificate') }}</flux:badge>
            @endif
            @if ($profile?->paklaring_path)
                <flux:badge variant="outline" size="sm" icon="document-text">{{ __('Paklaring') }}</flux:badge>
            @endif
            @if (!$profile?->ktp_path && !$profile?->portfolio_path && !$profile?->certificate_path && !$profile?->paklaring_path)
                <span class="text-xs text-zinc-400">{{ __('No documents uploaded') }}</span>
            @endif
        </div>
    </div>

    {{-- Audit Trail (Riwayat Perubahan Status) --}}
    @php
        $orderedLogs = $application->stageLogs
            ->reject(fn($log) => ($log->stage?->value ?? $log->stage) === \App\Enums\RecruitmentStage::APPLIED->value && $log->decision === 'passed')
            ->sortBy('created_at');
    @endphp
    @if($orderedLogs->isNotEmpty())
        <div class="col-span-2 sm:col-span-3 lg:col-span-4 mt-4 border-t border-zinc-200 dark:border-zinc-700 pt-4">
            <span
                class="text-xs font-medium uppercase tracking-wide text-zinc-400 mb-3 block">{{ __('Riwayat Perubahan Status') }}</span>
            <div class="space-y-4">
                <div class="relative pl-4 border-l-2 border-blue-400">
                    <div class="absolute -left-1.25 top-1.5 h-2 w-2 rounded-full bg-blue-500"></div>
                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                        {{ \App\Enums\RecruitmentStage::APPLIED->label() }}
                        <span
                            class="font-normal text-xs ms-1 px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">{{ __('Passed') }}</span>
                    </p>
                    <p class="text-xs text-zinc-500 mt-1">
                        {{ $application->created_at->format('d M Y, H:i') }} • {{ __('Oleh:') }}
                        {{ $application->candidate->name ?? 'Sistem' }}
                    </p>
                </div>

                @foreach ($orderedLogs as $log)
                    <div
                        class="relative pl-4 border-l-2 {{ $log->decision === 'rejected' ? 'border-red-400' : 'border-blue-400' }}">
                        <div
                            class="absolute -left-1.25 top-1.5 h-2 w-2 rounded-full {{ $log->decision === 'rejected' ? 'bg-red-500' : 'bg-blue-500' }}">
                        </div>
                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                            {{ $log->stage instanceof \App\Enums\RecruitmentStage ? $log->stage->label() : (\App\Enums\RecruitmentStage::tryFrom($log->stage)?->label() ?? $log->stage) }}
                            <span
                                class="font-normal text-xs ms-1 px-1.5 py-0.5 rounded {{ $log->decision === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">{{ ucfirst($log->decision) }}</span>
                        </p>
                        <p class="text-xs text-zinc-500 mt-1">
                            {{ $log->created_at->format('d M Y, H:i') }} • {{ __('Oleh:') }}
                            {{ $log->decidedBy?->name ?? 'Sistem' }}
                        </p>
                        @if($log->notes)
                            <p class="text-xs italic text-zinc-500 mt-1 block">"{{ $log->notes }}"</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>