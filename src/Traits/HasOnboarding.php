<?php

namespace Netizensgaurav\FilamentOnboarding\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Netizensgaurav\FilamentOnboarding\Models\OnboardingStep;
use Netizensgaurav\FilamentOnboarding\Models\UserOnboardingProgress;

trait HasOnboarding
{
    public function onboardingProgress(): HasMany
    {
        return $this->hasMany(UserOnboardingProgress::class);
    }

    public function hasCompletedOnboarding(): bool
    {
        return $this->onboarding_completed_at !== null;
    }

    public function hasSkippedOnboarding(): bool
    {
        return $this->onboarding_skipped === true;
    }

    public function markOnboardingAsComplete(): void
    {
        $this->update([
            'onboarding_completed_at' => now(),
            'onboarding_skipped' => false,
        ]);
    }

    public function markOnboardingAsSkipped(): void
    {
        $this->update([
            'onboarding_skipped' => true,
        ]);
    }

    public function resetOnboarding(): void
    {
        $this->update([
            'onboarding_completed_at' => null,
            'onboarding_skipped' => false,
        ]);

        $this->onboardingProgress()->delete();
    }

    public function getOnboardingCompletionPercentage(): float
    {
        $totalSteps = \Netizensgaurav\FilamentOnboarding\Models\OnboardingStep::active()->count();

        if ($totalSteps === 0) {
            return 100.0;
        }

        $completedSteps = $this->onboardingProgress()
            ->completed()
            ->count();

        return round(($completedSteps / $totalSteps) * 100, 2);
    }

    public function getCompletedOnboardingSteps()
    {
        return $this->onboardingProgress()
            ->completed()
            ->with('step')
            ->get();
    }

    public function getPendingOnboardingSteps()
    {
        $completedStepIds = $this->onboardingProgress()
            ->completed()
            ->pluck('onboarding_step_id');

        return OnboardingStep::active()
            ->ordered()
            ->whereNotIn('id', $completedStepIds)
            ->get();
    }

    public function hasCompletedStep(string $stepKey): bool
    {
        return $this->onboardingProgress()
            ->completed()
            ->whereHas('step', function ($query) use ($stepKey) {
                $query->where('key', $stepKey);
            })
            ->exists();
    }

    public function completeOnboardingStep(string $stepKey, array $data = []): void
    {
        $step = \Netizensgaurav\FilamentOnboarding\Models\OnboardingStep::where('key', $stepKey)->first();

        if (! $step) {
            return;
        }

        $progress = $this->onboardingProgress()->updateOrCreate(
            [
                'user_id' => $this->id,
                'onboarding_step_id' => $step->id,
                'team_id' => null,
            ],
            [
                'completed' => true,
                'completed_at' => now(),
                'data' => $data,
            ]
        );
    }
}
