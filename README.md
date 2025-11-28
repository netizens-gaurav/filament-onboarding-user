# Filament Onboarding User

Simple, flexible onboarding for Filament v4 applications. Users complete onboarding steps before accessing your app.

## Requirements

- PHP 8.2+
- Laravel 11+
- Filament 4.0+

## Installation

```bash
composer require netizens-gaurav/filament-onboarding-user
```

Run installation:

```bash
php artisan filament-onboarding:install
```

## Quick Start

### 1. Add Trait to User Model

```php
use Netizensgaurav\FilamentOnboarding\Traits\HasOnboarding;

class User extends Authenticatable
{
    use HasOnboarding;
    
    protected $fillable = [
        'name', 'email', 'password',
        'onboarding_completed_at', 'onboarding_skipped',
    ];

    protected $casts = [
        'onboarding_completed_at' => 'datetime',
        'onboarding_skipped' => 'boolean',
    ];
}
```

### 2. Register Plugin

In your `PanelProvider`:

```php
use Netizensgaurav\FilamentOnboarding\FilamentOnboardingPlugin;
use Netizensgaurav\FilamentOnboarding\Middleware\EnsureOnboardingComplete;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(
            FilamentOnboardingPlugin::make()
                ->mandatoryOnboarding(true) // Force completion
                ->skippable(false) // Don't allow skipping
        )
        ->authMiddleware([
            Authenticate::class,
            EnsureOnboardingComplete::class, // Add this
        ]);
}
```

**Done!** New users will see a simple profile form (name + email).

---

## Customization

### Create Your Own Onboarding

The default onboarding just asks for name and email. To customize:

**Step 1:** Create your onboarding page:

```bash
php artisan make:filament-page CustomOnboarding --type=custom
```

**Step 2:** Extend the base class:

```php
<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard;
use Netizensgaurav\FilamentOnboarding\Pages\OnboardingWizard;

class CustomOnboarding extends OnboardingWizard
{
    protected static string $view = 'filament-onboarding::pages.onboarding-wizard';
    
    protected function getWizardSteps(): array
    {
        return [
            Wizard\Step::make('profile')
                ->label('Your Profile')
                ->icon('heroicon-o-user')
                ->schema([
                    Section::make()
                        ->schema([
                            TextInput::make('name')->required(),
                            TextInput::make('email')->disabled(),
                            TextInput::make('phone'),
                            FileUpload::make('avatar')->image(),
                        ])
                        ->columns(2),
                ]),

            Wizard\Step::make('company')
                ->label('Company Info')
                ->icon('heroicon-o-building-office')
                ->schema([
                    TextInput::make('company_name')->required(),
                    TextInput::make('company_size'),
                ]),

            Wizard\Step::make('complete')
                ->label('All Set!')
                ->icon('heroicon-o-check-circle')
                ->schema([
                    Section::make()
                        ->schema([
                            \Filament\Forms\Components\Placeholder::make('completion')
                                ->content('🎉 Welcome aboard!')
                                ->columnSpanFull(),
                        ]),
                ]),
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        // Save your data
        $user->update([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
        ]);

        // Save company info (your logic)
        // $user->company()->create([...]);

        // Mark as complete
        $this->onboarded();

        // Redirect
        $this->redirect($this->getRedirectUrl());
    }
}
```

**Step 3:** Register your custom page:

```php
FilamentOnboardingPlugin::make()
    ->onboardingPage(\App\Filament\Pages\CustomOnboarding::class)
    ->mandatoryOnboarding(true)
```

---

## Examples

### Simple Profile + Preferences

```php
protected function getWizardSteps(): array
{
    return [
        Wizard\Step::make('profile')
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('phone'),
            ]),

        Wizard\Step::make('preferences')
            ->schema([
                Select::make('timezone')->options([...])->required(),
                Toggle::make('email_notifications')->default(true),
            ]),
    ];
}
```

### Team Creation (Multi-tenancy)

```php
protected function getWizardSteps(): array
{
    return [
        Wizard\Step::make('profile')
            ->schema([
                TextInput::make('name')->required(),
            ]),

        Wizard\Step::make('team')
            ->schema([
                TextInput::make('team_name')->required(),
                TextInput::make('team_slug')->required(),
            ]),
    ];
}

public function submit(): void
{
    $data = $this->form->getState();
    $user = auth()->user();

    // Create team
    $team = Team::create([
        'name' => $data['team_name'],
        'slug' => $data['team_slug'],
        'owner_id' => $user->id,
    ]);

    $team->members()->attach($user->id, ['role' => 'owner']);

    $this->onboarded();
    $this->redirect('/app');
}
```

### Subscription Selection

```php
protected function getWizardSteps(): array
{
    return [
        Wizard\Step::make('plan')
            ->schema([
                Radio::make('plan_id')
                    ->options(Plan::pluck('name', 'id'))
                    ->required(),
            ]),
    ];
}

public function submit(): void
{
    $data = $this->form->getState();
    
    auth()->user()->subscriptions()->create([
        'plan_id' => $data['plan_id'],
    ]);

    $this->onboarded();
    $this->redirect('/dashboard');
}
```

---

## Plugin Configuration

```php
FilamentOnboardingPlugin::make()
    ->enabled(true)                                    // Enable/disable
    ->mandatoryOnboarding(true)                        // Force completion
    ->skippable(false)                                 // Allow skip button
    ->onboardingPage(CustomOnboarding::class)          // Custom page
    ->afterOnboardingRedirectTo('/custom-url')         // Redirect URL
```

---

## User Methods

```php
$user = auth()->user();

// Check status
$user->hasCompletedOnboarding();        // bool
$user->hasSkippedOnboarding();          // bool

// Update status
$user->markOnboardingAsComplete();
$user->markOnboardingAsSkipped();
$user->resetOnboarding();               // Start over
```

---

## Config File

Edit `config/filament-onboarding.php`:

```php
return [
    'enabled' => true,
    'force_completion' => true,
    'allow_skipping' => false,
    
    'redirect' => [
        'after_completion' => null, // null = dashboard
    ],
];
```

---

## How It Works

1. User registers/logs in
2. Middleware checks if onboarding is complete
3. If not complete → redirect to onboarding page
4. User fills out form
5. Call `$this->onboarded()` to mark complete
6. Redirect to app

---

## Commands

```bash
# Install package
php artisan filament-onboarding:install

# Seed default steps (optional)
php artisan filament-onboarding:seed
```

---

## License

MIT License

## Support

- [GitHub Issues](https://github.com/netizens-gaurav/filament-onboarding-user/issues)

---

Made with ❤️ by [Gaurav Vaishnav](https://github.com/netizens-gaurav)
