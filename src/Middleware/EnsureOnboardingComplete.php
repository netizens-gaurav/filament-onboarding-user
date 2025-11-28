<?php

namespace Netizensgaurav\FilamentOnboarding\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Netizensgaurav\FilamentOnboarding\FilamentOnboardingPlugin;
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
        if (!$user) {
            return $next($request);
        }

        // Check if user model has HasOnboarding trait
        if (!method_exists($user, 'hasCompletedOnboarding')) {
            return $next($request);
        }

        // Skip if on onboarding page
        if ($request->routeIs('filament.*.pages.onboarding-wizard') ||
            $request->routeIs('filament.*.pages.custom-onboarding')) {
            return $next($request);
        }

        // Skip auth routes
        if ($request->routeIs('filament.*.auth.*')) {
            return $next($request);
        }

        // Check if mandatory onboarding is enabled
        $plugin = FilamentOnboardingPlugin::make();

        if ($plugin->isMandatory()) {
            // Check if user has completed or skipped onboarding
            if (!$user->hasCompletedOnboarding() && !$user->hasSkippedOnboarding()) {
                $panelId = filament()->getCurrentPanel()?->getId();

                if ($panelId) {
                    return redirect()->route("filament.{$panelId}.pages.onboarding-wizard");
                }
            }
        }

        return $next($request);
    }
}
