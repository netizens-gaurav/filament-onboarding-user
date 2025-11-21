<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable Onboarding
    |--------------------------------------------------------------------------
    | Control whether the onboarding system is active
    */
    'enabled' => env('FILAMENT_ONBOARDING_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Force Onboarding Completion
    |--------------------------------------------------------------------------
    | If true, users must complete onboarding before accessing the application
    */
    'force_completion' => env('FILAMENT_ONBOARDING_FORCE', true),

    /*
    |--------------------------------------------------------------------------
    | Skippable Steps
    |--------------------------------------------------------------------------
    | Allow users to skip optional steps during onboarding
    */
    'allow_skipping' => env('FILAMENT_ONBOARDING_ALLOW_SKIP', false),

    /*
    |--------------------------------------------------------------------------
    | Multi-Tenancy Support
    |--------------------------------------------------------------------------
    | Enable team/organization setup during onboarding
    */
    'multi_tenancy' => [
        'enabled' => env('FILAMENT_ONBOARDING_MULTI_TENANCY', false),
        'team_model' => env('FILAMENT_ONBOARDING_TEAM_MODEL', 'App\\Models\\Team'),
        'allow_team_creation' => true,
        'allow_team_joining' => true,
        'require_invitation_code' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Onboarding Steps Configuration
    |--------------------------------------------------------------------------
    | Define which steps are enabled and their settings
    */
    'steps' => [
        'profile' => [
            'enabled' => true,
            'required' => true,
            'order' => 1,
            'icon' => 'heroicon-o-user',
            'title' => 'Complete Your Profile',
            'description' => 'Tell us about yourself',
        ],
        'preferences' => [
            'enabled' => true,
            'required' => false,
            'order' => 2,
            'icon' => 'heroicon-o-cog-6-tooth',
            'title' => 'Set Your Preferences',
            'description' => 'Customize your experience',
        ],
        'team' => [
            'enabled' => false, // Enabled automatically when multi_tenancy is true
            'required' => false,
            'order' => 3,
            'icon' => 'heroicon-o-user-group',
            'title' => 'Join or Create Team',
            'description' => 'Collaborate with your team',
        ],
        'subscription' => [
            'enabled' => false,
            'required' => false,
            'order' => 4,
            'icon' => 'heroicon-o-credit-card',
            'title' => 'Choose Your Plan',
            'description' => 'Select the best plan for you',
        ],
        'tutorial' => [
            'enabled' => true,
            'required' => false,
            'order' => 5,
            'icon' => 'heroicon-o-light-bulb',
            'title' => 'Quick Tour',
            'description' => 'Learn about key features',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Profile Fields Configuration
    |--------------------------------------------------------------------------
    | Configure which profile fields to collect during onboarding
    */
    'profile_fields' => [
        'name' => ['enabled' => true, 'required' => true],
        'email' => ['enabled' => true, 'required' => true, 'readonly' => true],
        'phone' => ['enabled' => true, 'required' => false],
        'avatar' => ['enabled' => true, 'required' => false],
        'bio' => ['enabled' => false, 'required' => false],
        'company' => ['enabled' => false, 'required' => false],
        'job_title' => ['enabled' => false, 'required' => false],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preference Options
    |--------------------------------------------------------------------------
    */
    'preferences' => [
        'timezone' => [
            'enabled' => true,
            'required' => true,
            'default' => 'UTC',
        ],
        'language' => [
            'enabled' => true,
            'required' => false,
            'default' => 'en',
            'options' => [
                'en' => 'English',
                'es' => 'Spanish',
                'fr' => 'French',
                'de' => 'German',
            ],
        ],
        'date_format' => [
            'enabled' => false,
            'required' => false,
            'default' => 'Y-m-d',
        ],
        'time_format' => [
            'enabled' => false,
            'required' => false,
            'default' => 'H:i',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'email_notifications' => [
            'enabled' => true,
            'default' => true,
        ],
        'push_notifications' => [
            'enabled' => false,
            'default' => false,
        ],
        'marketing_emails' => [
            'enabled' => true,
            'default' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Redirect Configuration
    |--------------------------------------------------------------------------
    */
    'redirect' => [
        'after_completion' => null, // null = default dashboard
        'after_skip' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    */
    'user_model' => env('FILAMENT_ONBOARDING_USER_MODEL', 'App\\Models\\User'),

    /*
    |--------------------------------------------------------------------------
    | Database Tables
    |--------------------------------------------------------------------------
    */
    'tables' => [
        'onboarding_steps' => 'onboarding_steps',
        'user_onboarding_progress' => 'user_onboarding_progress',
    ],

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    | Enable/disable event dispatching
    */
    'events' => [
        'enabled' => true,
    ],
];
