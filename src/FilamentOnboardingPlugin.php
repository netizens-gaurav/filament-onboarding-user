<?php

namespace Netizensgaurav\FilamentOnboarding;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Netizensgaurav\FilamentOnboarding\Pages\OnboardingWizard;

class FilamentOnboardingPlugin implements Plugin
{
    protected bool $enabled = true;

    protected bool $mandatoryOnboarding = false;

    protected bool $skippable = false;

    protected ?string $onboardingPage = null;

    protected ?string $redirectTo = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-onboarding';
    }

    public function register(Panel $panel): void
    {
        if (! $this->enabled) {
            return;
        }

        $pageClass = $this->onboardingPage ?? OnboardingWizard::class;

        $panel->pages([
            $pageClass,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    /**
     * Enable/disable the onboarding
     */
    public function enabled(bool $condition = true): static
    {
        $this->enabled = $condition;

        return $this;
    }

    /**
     * Force users to complete onboarding
     */
    public function mandatoryOnboarding(bool $condition = true): static
    {
        $this->mandatoryOnboarding = $condition;

        return $this;
    }

    /**
     * Allow users to skip onboarding
     */
    public function skippable(bool $condition = true): static
    {
        $this->skippable = $condition;

        return $this;
    }

    /**
     * Set custom onboarding page class
     */
    public function onboardingPage(string $pageClass): static
    {
        $this->onboardingPage = $pageClass;

        return $this;
    }

    /**
     * Set redirect URL after onboarding completion
     */
    public function afterOnboardingRedirectTo(string $url): static
    {
        $this->redirectTo = $url;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isMandatory(): bool
    {
        return $this->mandatoryOnboarding;
    }

    public function isSkippable(): bool
    {
        return $this->skippable;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectTo;
    }
}
