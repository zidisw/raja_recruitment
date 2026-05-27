<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title . ' - ' . config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<script>
    (function () {
        function readStorage(key) {
            try {
                return window.localStorage.getItem(key);
            } catch (e) {
                return null;
            }
        }

        function themePalette(isDark) {
            return isDark
                ? {
                    surface: '#0f0f1a',
                    page: 'linear-gradient(135deg, #0f0f1a 0%, #1a0f2e 25%, #1a1a0f 50%, #0f1a1a 75%, #0f0f1a 100%)',
                }
                : {
                    surface: '#fafafa',
                    page: 'linear-gradient(135deg, #f0f4ff 0%, #faf5ff 25%, #fff7ed 50%, #f0fdf4 75%, #f0f4ff 100%)',
                };
        }

        function resolveAppearance() {
            var appearance = readStorage('flux.appearance');
            var legacyTheme = readStorage('theme');

            if (appearance) {
                return appearance;
            }

            if (legacyTheme) {
                return legacyTheme;
            }

            return 'light';
        }

        window.__resolvePreferredAppearance = resolveAppearance;

        window.__applyPreferredTheme = function (appearanceOverride) {
            var appearance = appearanceOverride || resolveAppearance();

            if (appearance !== 'light' && appearance !== 'dark' && appearance !== 'system') {
                appearance = 'light';
            }

            var shouldUseDark = appearance === 'dark'
                || (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            var palette = themePalette(shouldUseDark);

            document.documentElement.classList.toggle('dark', shouldUseDark);
            document.documentElement.dataset.theme = shouldUseDark ? 'dark' : 'light';
            document.documentElement.style.colorScheme = shouldUseDark ? 'dark' : 'light';

            // Sync theme to a cookie for SSR persistence preventing Livewire navigation flashes
            document.cookie = 'theme=' + appearance + ';path=/;max-age=31536000';
            document.documentElement.style.setProperty('--page-surface', palette.surface);
            document.documentElement.style.setProperty('--page-background', palette.page);
            document.documentElement.style.backgroundColor = palette.surface;

            if (document.body) {
                document.body.dataset.theme = shouldUseDark ? 'dark' : 'light';
                document.body.style.backgroundColor = palette.surface;
            }

            if (window.Flux && typeof window.Flux === 'object' && 'appearance' in window.Flux) {
                window.Flux.appearance = appearance;
            }

            return shouldUseDark;
        };

        try {
            window.__applyPreferredTheme();
        } catch (e) {
            // Ignore storage access issues and fall back to default render.
        }

        window.__freezeThemeForNavigation = function () {
            if (typeof window.__applyPreferredTheme === 'function') {
                window.__applyPreferredTheme();
            }

            document.documentElement.classList.add('theme-nav-freeze');
        };
    })();
</script>

<link rel="icon" href="{{ asset('raja.svg') }}" type="image/svg+xml">
<link rel="apple-touch-icon" href="{{ asset('raja.svg') }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link rel="preload" href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" as="style"
    onload="this.onload=null;this.rel='stylesheet'">
<noscript>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet">
</noscript>

@livewireStyles

{{-- Flux is the single source of truth for dashboard theme (uses flux.appearance key) --}}
@fluxAppearance

<style>
    :root {
        --page-surface: #fafafa;
        --page-background: linear-gradient(135deg, #f0f4ff 0%, #faf5ff 25%, #fff7ed 50%, #f0fdf4 75%, #f0f4ff 100%);
    }

    html {
        background-color: var(--page-surface);
        background-image: var(--page-background);
    }

    html.dark {
        background-color: var(--page-surface);
    }

    body {
        background-color: var(--page-surface);
        background-image: var(--page-background);
    }

    [x-cloak] {
        display: none !important;
    }

    /* Prevent brief light/dark transition flashes during Livewire SPA swaps. */
    html.theme-nav-freeze,
    html.theme-nav-freeze *,
    html.theme-nav-freeze *::before,
    html.theme-nav-freeze *::after {
        transition: none !important;
        animation: none !important;
    }
</style>

<script data-navigate-once>
    (function () {
        if (window.__dashboardThemeBooted) {
            if (typeof window.__applyPreferredTheme === 'function') {
                window.__applyPreferredTheme();
            }

            return;
        }

        window.__dashboardThemeBooted = true;

        // Migrate legacy key once, then let Flux manage appearance lifecycle.
        var oldTheme = null;

        try {
            oldTheme = window.localStorage.getItem('theme');
            if (oldTheme && !window.localStorage.getItem('flux.appearance')) {
                window.localStorage.setItem('flux.appearance', oldTheme);
            }
            if (oldTheme) {
                window.localStorage.removeItem('theme');
            }

            // One-time reset after dark-only rollout: return dashboard default to light.
            var migrationKey = 'dashboard-appearance-default-light-v1';
            if (!window.localStorage.getItem(migrationKey)) {
                window.localStorage.setItem('flux.appearance', 'light');
                window.localStorage.setItem(migrationKey, '1');
            } else if (!window.localStorage.getItem('flux.appearance')) {
                window.localStorage.setItem('flux.appearance', 'light');
            }
        } catch (e) {
            oldTheme = null;
        }

        // Sync local storage state to cookie immediately to guarantee SSR stability on next request
        var currentAppearance = window.__resolvePreferredAppearance ? window.__resolvePreferredAppearance() : window.localStorage.getItem('flux.appearance');
        if (currentAppearance) {
            document.cookie = 'theme=' + currentAppearance + ';path=/;max-age=31536000';
        }

        if (typeof window.__applyPreferredTheme === 'function') {
            window.__applyPreferredTheme();
        }

        function prepareNavigateTheme(event) {
            var target = event.target instanceof Element ? event.target.closest('[wire\\:navigate]') : null;

            if (!target || typeof window.__freezeThemeForNavigation !== 'function') {
                return;
            }

            window.__freezeThemeForNavigation();
        }

        document.addEventListener('pointerdown', prepareNavigateTheme, true);
        document.addEventListener('click', prepareNavigateTheme, true);
        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            prepareNavigateTheme(event);
        }, true);

        // Keep dark class stable during html morph to avoid light flash.
        document.addEventListener('livewire:init', function () {
            Livewire.hook('morph.updating', function ({ el, toEl }) {
                if (el === document.documentElement) {
                    toEl.classList.toggle('dark', document.documentElement.classList.contains('dark'));
                    toEl.dataset.theme = document.documentElement.dataset.theme || '';
                    toEl.style.colorScheme = document.documentElement.style.colorScheme;
                    toEl.style.setProperty('--page-surface', document.documentElement.style.getPropertyValue('--page-surface'));
                    toEl.style.setProperty('--page-background', document.documentElement.style.getPropertyValue('--page-background'));
                    toEl.style.backgroundColor = document.documentElement.style.backgroundColor;
                }

                if (el === document.body) {
                    toEl.dataset.theme = document.body.dataset.theme || document.documentElement.dataset.theme || '';
                    toEl.style.backgroundColor = document.body.style.backgroundColor;
                }
            });

            Livewire.hook('morph.updated', function ({ el }) {
                if ((el === document.documentElement || el === document.body) && typeof window.__applyPreferredTheme === 'function') {
                    window.__applyPreferredTheme();
                }
            });
        });

        // Only handle transition freeze during SPA navigation to avoid flash.
        document.addEventListener('livewire:navigating', function (event) {
            if (typeof window.__freezeThemeForNavigation === 'function') {
                window.__freezeThemeForNavigation();
            }

            if (event.detail && typeof event.detail.onSwap === 'function') {
                event.detail.onSwap(function () {
                    if (typeof window.__applyPreferredTheme === 'function') {
                        window.__applyPreferredTheme();
                    }
                });
            }
        });

        document.addEventListener('livewire:navigated', function () {
            if (typeof window.__applyPreferredTheme === 'function') {
                window.__applyPreferredTheme();
            }
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    document.documentElement.classList.remove('theme-nav-freeze');
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            if (typeof window.__applyPreferredTheme === 'function') {
                window.__applyPreferredTheme();
            }
        });
    })();
</script>

@vite(['resources/css/app.css', 'resources/js/app.js'])