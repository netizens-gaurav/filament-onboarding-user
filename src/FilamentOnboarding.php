<?php

namespace Netizensgaurav\FilamentOnboarding;

class FilamentOnboarding
{
    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function isEnabled(): bool
    {
        return config('filament-onboarding.enabled', true);
    }

    public function shouldForceCompletion(): bool
    {
        return config('filament-onboarding.force_completion', true);
    }

    public function canSkip(): bool
    {
        return config('filament-onboarding.allow_skipping', false);
    }

    public function isMultiTenancyEnabled(): bool
    {
        return config('filament-onboarding.multi_tenancy.enabled', false);
    }
}
