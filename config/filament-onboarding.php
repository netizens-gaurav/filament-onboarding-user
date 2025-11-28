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

        // The tenant model (Team, Company, Organization, Store, Workspace...)
        'tenant_model' => env('FILAMENT_ONBOARDING_TENANT_MODEL', null),

        // The table & column used for FK reference
        'tenant_table' => env('FILAMENT_ONBOARDING_TENANT_TABLE', null), // e.g. 'teams', 'companies'
        'tenant_column' => env('FILAMENT_ONBOARDING_TENANT_COLUMN', 'tenant_id'),

        // Whether FK should be added if table exists
        'enable_tenant_fk' => true,

        'allow_team_creation' => true,
        'allow_team_joining' => true,
        'require_invitation_code' => false,
    ],

    'notifications' => [
        'email_notifications' => ['enabled' => true, 'default' => true],
        'push_notifications' => ['enabled' => false, 'default' => false],
        'marketing_emails' => ['enabled' => true, 'default' => false],
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
