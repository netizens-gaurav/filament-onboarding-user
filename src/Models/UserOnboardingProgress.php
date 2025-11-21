<?php

namespace Netizensgaurav\FilamentOnboarding\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOnboardingProgress extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'onboarding_step_id',
        'team_id',
        'completed',
        'completed_at',
        'data',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        //
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'completed_at' => 'datetime',
            'data' => 'array',
        ];
    }

    public function getTable(): string
    {
        return config('filament-onboarding.tables.user_onboarding_progress', 'user_onboarding_progress');
    }

    public function user(): BelongsTo
    {
        $userModel = config('filament-onboarding.user_model', 'App\\Models\\User');
        return $this->belongsTo($userModel);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(OnboardingStep::class, 'onboarding_step_id');
    }

    public function team(): BelongsTo
    {
        $teamModel = config('filament-onboarding.multi_tenancy.team_model', 'App\\Models\\Team');
        return $this->belongsTo($teamModel);
    }

    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('completed', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForTeam($query, $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'completed' => true,
            'completed_at' => now(),
        ]);
    }
}
