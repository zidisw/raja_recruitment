<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCandidateProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasUserRole()) {
            return $next($request);
        }

        // Skip if already on the setup page
        if ($request->routeIs('candidate.profile.setup')) {
            return $next($request);
        }

        // Redirect to profile setup if no profile exists
        if (! $user->profile) {
            return redirect()->route('candidate.profile.setup');
        }

        return $next($request);
    }
}
