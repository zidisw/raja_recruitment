@props([
    'sidebar' => false,
])

@if ($sidebar)
    <flux:sidebar.brand href="{{ url('/') }}" {{ $attributes }}>
        <x-slot name="logo" class="h-8! min-w-0! rounded-none! overflow-visible!">
            {{-- rajaori.svg for dark (white background), rajawhite.svg for light (dark background) --}}
            <img src="{{ asset('rajawhite.svg') }}" alt="PT. Roda Jaya Sakti"
                class="h-7 w-auto max-w-none object-contain hidden dark:block">
            <img src="{{ asset('rajaori.svg') }}" alt="PT. Roda Jaya Sakti"
                class="h-7 w-auto max-w-none object-contain block dark:hidden">
        </x-slot>
    </flux:sidebar.brand>
@else
    {{-- Auth pages - link to dashboard if logged in, otherwise home --}}
    @php
        $logoHref = '/';

        if (auth()->check()) {
            $authUser = auth()->user();

            if ($authUser->hasUserRole()) {
                $logoHref = $authUser->profile ? route('candidate.dashboard') : route('candidate.profile.setup');
            } else {
                $logoHref = route('dashboard');
            }
        }
    @endphp
    <a href="{{ $logoHref }}"
        {{ $attributes->merge(['class' => 'flex items-center justify-center transition-opacity hover:opacity-80']) }}>
        <img src="{{ asset('rajawhite.svg') }}" alt="PT. Roda Jaya Sakti"
            class="h-16 w-auto object-contain hidden dark:block">
        <img src="{{ asset('rajaori.svg') }}" alt="PT. Roda Jaya Sakti"
            class="h-16 w-auto object-contain block dark:hidden">
    </a>
@endif
