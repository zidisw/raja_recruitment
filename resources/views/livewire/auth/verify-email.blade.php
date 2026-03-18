<x-layouts::auth :title="__('Verify Email')">
    <div x-data="{
        isSubmitting: false,
        mounted: false,
        progress: 0,
        startProgress() {
            this.isSubmitting = true;
            this.progress = 10;
            let interval = setInterval(() => {
                if (!this.isSubmitting) { clearInterval(interval); return; }
                this.progress += (95 - this.progress) * 0.08;
            }, 150);
        }
    }" x-init="setTimeout(() => mounted = true, 50)"
        class="flex flex-col gap-8 w-full max-w-md mx-auto transition-all duration-500 opacity-0 translate-y-4"
        :class="mounted ? '!opacity-100 !translate-y-0' : ''">

        <div class="text-center space-y-4">
            <div
                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-brand-50 dark:bg-brand-900/30 text-brand-500 mb-2">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                Verify your email
            </h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the
                link we just emailed to you? If you didn't receive the email, we will gladly send you another.
            </p>
            <p class="text-xs font-medium text-brand-600 dark:text-brand-400">
                The verification link will expire in 1 hour.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)" x-show="show" x-transition.opacity.duration.300ms
                class="p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-sm text-emerald-600 dark:text-emerald-400 text-center">
                A new verification link has been sent to the email address you provided during registration.
            </div>
        @endif

        <div class="flex flex-col gap-4 mt-2">
            <form method="POST" action="{{ route('verification.send') }}" @submit="startProgress()">
                @csrf
                <button type="submit" :disabled="isSubmitting"
                    class="w-full flex items-center justify-center px-4 py-3 text-sm font-semibold text-white bg-brand-500 hover:bg-brand-600 rounded-lg shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                    <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                        viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span x-text="isSubmitting ? 'Sending...' : 'Resend Verification Email'"></span>
                </button>
            </form>

            <div x-show="isSubmitting" class="w-full h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden"
                style="display: none;">
                <div class="h-full bg-brand-500 transition-all duration-200 ease-out" :style="`width: ${progress}%`">
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full px-4 py-3 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-lg transition-colors duration-200">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</x-layouts::auth>
