<?php

namespace Netizensgaurav\FilamentOnboarding\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class OnboardingWizard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    protected static string $view = 'filament-onboarding::pages.onboarding-wizard';

    protected static bool $shouldRegisterNavigation = false;
    
    protected static ?string $title = 'Welcome! Let\'s Get Started';

    public ?array $data = [];

    public function mount(): void
    {
        // Redirect if already onboarded (unless explicitly accessing)
        if (Auth::user()->hasCompletedOnboarding()) {
            $this->redirect(filament()->getUrl());
        }

        $this->form->fill($this->getDefaultData());
    }

    protected function getDefaultData(): array
    {
        $user = Auth::user();

        return [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'timezone' => $user->timezone ?? config('app.timezone', 'UTC'),
            'language' => $user->language ?? 'en',
            'email_notifications' => $user->email_notifications ?? true,
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make($this->getWizardSteps())
                    ->submitAction(view('filament-onboarding::components.submit-button'))
                    ->skippable(config('filament-onboarding.allow_skipping', false))
                    ->startOnStep($this->getCurrentStep())
            ])
            ->statePath('data');
    }

    protected function getWizardSteps(): array
    {
        $steps = [];
        $config = config('filament-onboarding.steps', []);

        // Profile Step
        if ($config['profile']['enabled'] ?? true) {
            $steps[] = $this->getProfileStep();
        }

        // Preferences Step
        if ($config['preferences']['enabled'] ?? true) {
            $steps[] = $this->getPreferencesStep();
        }

        // Team Step (Multi-tenancy)
        if (config('filament-onboarding.multi_tenancy.enabled') && ($config['team']['enabled'] ?? false)) {
            $steps[] = $this->getTeamStep();
        }

        // Completion Step
        $steps[] = $this->getCompletionStep();

        return $steps;
    }

    protected function getProfileStep(): Wizard\Step
    {
        $profileFields = config('filament-onboarding.profile_fields', []);

        return Wizard\Step::make('Profile')
            ->icon('heroicon-o-user')
            ->description('Complete your profile information')
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required($profileFields['name']['required'] ?? true)
                            ->maxLength(255)
                            ->visible($profileFields['name']['enabled'] ?? true),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required($profileFields['email']['required'] ?? true)
                            ->disabled($profileFields['email']['readonly'] ?? true)
                            ->visible($profileFields['email']['enabled'] ?? true),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20)
                            ->required($profileFields['phone']['required'] ?? false)
                            ->visible($profileFields['phone']['enabled'] ?? true),

                        FileUpload::make('avatar')
                            ->label('Profile Photo')
                            ->image()
                            ->avatar()
                            ->directory('avatars')
                            ->maxSize(2048)
                            ->required($profileFields['avatar']['required'] ?? false)
                            ->visible($profileFields['avatar']['enabled'] ?? true),

                        Textarea::make('bio')
                            ->label('Bio')
                            ->rows(3)
                            ->maxLength(500)
                            ->required($profileFields['bio']['required'] ?? false)
                            ->visible($profileFields['bio']['enabled'] ?? false),

                        TextInput::make('company')
                            ->label('Company')
                            ->maxLength(255)
                            ->required($profileFields['company']['required'] ?? false)
                            ->visible($profileFields['company']['enabled'] ?? false),

                        TextInput::make('job_title')
                            ->label('Job Title')
                            ->maxLength(255)
                            ->required($profileFields['job_title']['required'] ?? false)
                            ->visible($profileFields['job_title']['enabled'] ?? false),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getPreferencesStep(): Wizard\Step
    {
        $preferences = config('filament-onboarding.preferences', []);
        $notifications = config('filament-onboarding.notifications', []);

        return Wizard\Step::make('Preferences')
            ->icon('heroicon-o-cog-6-tooth')
            ->description('Customize your experience')
            ->schema([
                Section::make('Regional Settings')
                    ->schema([
                        Select::make('timezone')
                            ->label('Timezone')
                            ->options($this->getTimezoneOptions())
                            ->searchable()
                            ->required($preferences['timezone']['required'] ?? true)
                            ->default($preferences['timezone']['default'] ?? 'UTC')
                            ->visible($preferences['timezone']['enabled'] ?? true),

                        Select::make('language')
                            ->label('Language')
                            ->options($preferences['language']['options'] ?? [
                                'en' => 'English',
                                'es' => 'Spanish',
                                'fr' => 'French',
                                'de' => 'German',
                            ])
                            ->default($preferences['language']['default'] ?? 'en')
                            ->required($preferences['language']['required'] ?? false)
                            ->visible($preferences['language']['enabled'] ?? true),
                    ])
                    ->columns(2),

                Section::make('Notification Preferences')
                    ->schema([
                        Toggle::make('email_notifications')
                            ->label('Email Notifications')
                            ->helperText('Receive email notifications for important updates')
                            ->default($notifications['email_notifications']['default'] ?? true)
                            ->visible($notifications['email_notifications']['enabled'] ?? true),

                        Toggle::make('push_notifications')
                            ->label('Push Notifications')
                            ->helperText('Receive push notifications in your browser')
                            ->default($notifications['push_notifications']['default'] ?? false)
                            ->visible($notifications['push_notifications']['enabled'] ?? false),

                        Toggle::make('marketing_emails')
                            ->label('Marketing Emails')
                            ->helperText('Receive news, tips, and special offers')
                            ->default($notifications['marketing_emails']['default'] ?? false)
                            ->visible($notifications['marketing_emails']['enabled'] ?? true),
                    ]),
            ]);
    }

    protected function getTeamStep(): Wizard\Step
    {
        return Wizard\Step::make('Team')
            ->icon('heroicon-o-user-group')
            ->description('Join or create a team')
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('team_action')
                            ->label('What would you like to do?')
                            ->options([
                                'create' => 'Create a new team',
                                'join' => 'Join an existing team',
                                'skip' => 'Skip for now',
                            ])
                            ->required()
                            ->reactive()
                            ->default('create'),

                        TextInput::make('team_name')
                            ->label('Team Name')
                            ->maxLength(255)
                            ->visible(fn ($get) => $get('team_action') === 'create')
                            ->required(fn ($get) => $get('team_action') === 'create'),

                        TextInput::make('invitation_code')
                            ->label('Invitation Code')
                            ->maxLength(100)
                            ->visible(fn ($get) => $get('team_action') === 'join')
                            ->required(fn ($get) => $get('team_action') === 'join'),
                    ]),
            ]);
    }

    protected function getCompletionStep(): Wizard\Step
    {
        return Wizard\Step::make('Complete')
            ->icon('heroicon-o-check-circle')
            ->description('You\'re all set!')
            ->schema([
                Section::make()
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('completion_message')
                            ->content('🎉 Congratulations! Your account is now set up and ready to go. Click the button below to get started.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getCurrentStep(): int
    {
        // Logic to determine which step to start on
        return 1;
    }

    protected function getTimezoneOptions(): array
    {
        $timezones = timezone_identifiers_list();
        return array_combine($timezones, $timezones);
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        try {
            // Update user profile
            $user->update([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'avatar' => $data['avatar'] ?? null,
                'timezone' => $data['timezone'] ?? 'UTC',
                'language' => $data['language'] ?? 'en',
                'email_notifications' => $data['email_notifications'] ?? true,
            ]);

            // Handle team setup if multi-tenancy is enabled
            if (config('filament-onboarding.multi_tenancy.enabled')) {
                $this->handleTeamSetup($data);
            }

            // Mark onboarding as complete
            $user->markOnboardingAsComplete();

            // Complete all steps
            $this->completeAllSteps();

            // Show success notification
            Notification::make()
                ->success()
                ->title('Onboarding Complete!')
                ->body('Welcome aboard! Your account has been set up successfully.')
                ->send();

            // Redirect to dashboard or custom URL
            $redirectUrl = config('filament-onboarding.redirect.after_completion') ?? filament()->getUrl();
            $this->redirect($redirectUrl);

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Something went wrong. Please try again.')
                ->send();
        }
    }

    protected function handleTeamSetup(array $data): void
    {
        // Implement team creation or joining logic
        if (($data['team_action'] ?? '') === 'create' && !empty($data['team_name'])) {
            // Create team logic here
        } elseif (($data['team_action'] ?? '') === 'join' && !empty($data['invitation_code'])) {
            // Join team logic here
        }
    }

    protected function completeAllSteps(): void
    {
        $steps = \Netizensgaurav\FilamentOnboarding\Models\OnboardingStep::active()->get();

        foreach ($steps as $step) {
            Auth::user()->completeOnboardingStep($step->key);
        }
    }

    public function skipOnboarding(): void
    {
        if (!config('filament-onboarding.allow_skipping', false)) {
            return;
        }

        Auth::user()->markOnboardingAsSkipped();

        Notification::make()
            ->info()
            ->title('Onboarding Skipped')
            ->body('You can complete your profile anytime from settings.')
            ->send();

        $this->redirect(filament()->getUrl());
    }
}
