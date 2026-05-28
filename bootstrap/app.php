<?php

if (
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
    (isset($_SERVER['HTTP_CF_VISITOR']) && str_contains($_SERVER['HTTP_CF_VISITOR'], 'https')) ||
    (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'rodajayasakti.id' || $_SERVER['HTTP_HOST'] === 'www.rodajayasakti.id'))
) {
    $_SERVER['HTTPS'] = 'on';
}

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \App\Http\Middleware\DisableCache::class,
        ]);

        $middleware->alias([
            'candidate.profile.complete' => \App\Http\Middleware\EnsureCandidateProfileComplete::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $e) {
            try {
                \Illuminate\Support\Facades\Log::build([
                    'driver' => 'single',
                    'path' => storage_path('logs/csrf_debug.log'),
                ])->error('Exception caught in handler: '.get_class($e), [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => substr($e->getTraceAsString(), 0, 2000), // limit trace length
                ]);
            } catch (\Throwable $loggingError) {
                // Ignore errors during logging to prevent infinite loops
            }
        });

        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if (method_exists($e, 'getStatusCode') && $e->getStatusCode() === 419) {
                try {
                    \Illuminate\Support\Facades\Log::build([
                        'driver' => 'single',
                        'path' => storage_path('logs/csrf_debug.log'),
                    ])->error('419 Page Expired detected in render phase: '.get_class($e), [
                        'message' => $e->getMessage(),
                        'url' => $request->fullUrl(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => substr($e->getTraceAsString(), 0, 2000),
                    ]);
                } catch (\Throwable) {
                    // Ignore
                }
            }

            return null; // Continue standard rendering
        });
    })->create();
