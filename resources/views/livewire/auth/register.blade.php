<x-layouts::auth :title="__('Register')">
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

        <div class="text-center space-y-2">
            <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                Create an account
            </h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Enter your details below to join PT. Roda Jaya Sakti
            </p>
        </div>

        @if (session('status'))
            <div
                class="p-4 rounded-lg bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 text-sm text-center text-zinc-600 dark:text-zinc-400">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" @submit="startProgress()" class="flex flex-col gap-5">
            @csrf

            <div class="space-y-1.5">
                <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Full
                    Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                    autocomplete="name" placeholder="John Doe"
                    class="block w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:border-brand-500 focus:ring-brand-500 dark:focus:border-brand-500 dark:focus:ring-brand-500 transition-colors duration-200">
                @error('name')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="email" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Email
                    Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    autocomplete="email" placeholder="email@example.com"
                    class="block w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:border-brand-500 focus:ring-brand-500 dark:focus:border-brand-500 dark:focus:ring-brand-500 transition-colors duration-200">
                @error('email')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="password"
                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Password</label>
                <input type="password" id="password" name="password" required autocomplete="new-password"
                    placeholder="••••••••"
                    class="block w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:border-brand-500 focus:ring-brand-500 dark:focus:border-brand-500 dark:focus:ring-brand-500 transition-colors duration-200">
                @error('password')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="password_confirmation"
                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    autocomplete="new-password" placeholder="••••••••"
                    class="block w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:border-brand-500 focus:ring-brand-500 dark:focus:border-brand-500 dark:focus:ring-brand-500 transition-colors duration-200">
            </div>

            <button type="submit" :disabled="isSubmitting"
                class="mt-2 w-full flex items-center justify-center px-4 py-3 text-sm font-semibold text-white bg-brand-500 hover:bg-brand-600 rounded-lg shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                    viewBox="0 0 24 24" style="display: none;">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span x-text="isSubmitting ? 'Creating account...' : 'Create account'"></span>
            </button>
        </form>

        <div x-show="isSubmitting" class="w-full h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden"
            style="display: none;">
            <div class="h-full bg-brand-500 transition-all duration-200 ease-out" :style="`width: ${progress}%`"></div>
        </div>

        <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
            Already have an account?
            <a href="{{ route('login') }}" wire:navigate
                class="font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300 transition-colors">
                Log in
            </a>
        </div>
    </div>
</x-layouts::auth>
