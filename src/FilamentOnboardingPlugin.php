<?php

namespace Netizensgaurav\FilamentOnboarding;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Netizensgaurav\FilamentOnboarding\Pages\OnboardingWizard;

class FilamentOnboardingPlugin implements Plugin
{
    protected bool $enabled = true;

    protected bool $forceCompletion = true;

    protected bool $allowSkipping = false;

    protected bool $multiTenancy = false;

    protected array $steps = [];

    protected ?string $redirectAfterCompletion = null;

    public function getId(): string
    {
        return 'filament-onboarding';
    }

    public function register(Panel $panel): void
    {
        if (!$this->enabled) {
            return;
        }

        $panel->pages([
            OnboardingWizard::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function enabled(bool $condition = true): static
    {
        $this->enabled = $condition;
        return $this;
    }

    public function forceCompletion(bool $condition = true): static
    {
        $this->forceCompletion = $condition;
        return $this;
    }

    public function allowSkipping(bool $condition = true): static
    {
        $this->allowSkipping = $condition;
        return $this;
    }

    public function multiTenancy(bool $enabled = true): static
    {
        $this->multiTenancy = $enabled;
        return $this;
    }

    public function steps(array $steps): static
    {
        $this->steps = $steps;
        return $this;
    }

    public function redirectAfterCompletion(?string $url): static
    {
        $this->redirectAfterCompletion = $url;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function shouldForceCompletion(): bool
    {
        return $this->forceCompletion;
    }

    public function canSkip(): bool
    {
        return $this->allowSkipping;
    }

    public function isMultiTenancy(): bool
    {
        return $this->multiTenancy;
    }

    public function getSteps(): array
    {
        return $this->steps ?: config('filament-onboarding.steps', []);
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectAfterCompletion ?? config('filament-onboarding.redirect.after_completion');
    }
}
