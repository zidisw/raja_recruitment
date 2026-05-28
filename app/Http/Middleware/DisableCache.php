<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Add headers to disable LiteSpeed Cache and standard browser cache
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');

        // Log details if response is 419 Page Expired (CSRF Mismatch)
        if ($response->getStatusCode() === 419) {
            try {
                $session = $request->hasSession() ? $request->session() : null;
                $sessionToken = $session ? $session->token() : 'no_session';
                $requestToken = $request->header('X-CSRF-TOKEN') ?? $request->input('_token') ?? 'no_token';
                
                $logData = [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'is_secure' => $request->secure() ? 'yes' : 'no',
                    'request_token' => $requestToken,
                    'session_token' => $sessionToken,
                    'session_id' => $session ? $session->getId() : 'no_session_id',
                    'cookies' => $request->cookies->all(),
                ];
                
                \Illuminate\Support\Facades\Log::build([
                    'driver' => 'single',
                    'path' => storage_path('logs/csrf_debug.log'),
                ])->error('CSRF 419 Mismatch Detected', $logData);
            } catch (\Exception $e) {
                // Ignore exceptions during logging
            }
        }

        return $response;
    }
}
