<?php

namespace Netizensgaurav\FilamentOnboarding\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if onboarding is enabled
        if (! config('filament-onboarding.enabled', true)) {
            return $next($request);
        }

        $user = Auth::user();

        // Allow guests
        if (! $user) {
            return $next($request);
        }

        // Skip if on onboarding page
        if ($request->routeIs('filament.*.pages.onboarding-wizard')) {
            return $next($request);
        }

        // Skip if on logout route
        if ($request->routeIs('filament.*.auth.logout')) {
            return $next($request);
        }

        // Check if force completion is enabled
        $forceCompletion = config('filament-onboarding.force_completion', true);

        if ($forceCompletion) {
            // Check if user has completed or skipped onboarding
            if (! $user->hasCompletedOnboarding() && ! $user->hasSkippedOnboarding()) {
                $panelId = filament()->getCurrentPanel()->getId();

                return redirect()->route("filament.{$panelId}.pages.onboarding-wizard");
            }
        }

        return $next($request);
    }
}
