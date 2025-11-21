<?php

namespace Netizensgaurav\FilamentOnboarding\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingStep extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'title',
        'description',
        'icon',
        'order',
        'required',
        'is_active',
        'config',
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
            'required' => 'boolean',
            'is_active' => 'boolean',
            'config' => 'array',
            'order' => 'integer',
        ];
    }

    public function getTable(): string
    {
        return config('onboarding.tables.onboarding_steps', 'onboarding_steps');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(UserOnboardingProgress::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeRequired($query)
    {
        return $query->where('required', true);
    }
}
