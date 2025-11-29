<?php

namespace Netizensgaurav\FilamentOnboarding\Pages;

use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\HtmlString;

class OnboardingWizard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected string $view = 'filament-onboarding::pages.onboarding-wizard';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Welcome! Let\'s Get Started';

    public ?array $data = [];

    public function mount(): void
    {
        // Redirect if already onboarded
        if (Auth::user()->hasCompletedOnboarding()) {
            $this->redirect(filament()->getUrl());
        }

        $this->form->fill([
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make($this->getWizardSteps())
                    ->submitAction(new HtmlString(view('filament-onboarding::components.submit-button')->render()))
                    ->skippable($this->canSkip()),
            ])
            ->statePath('data');
    }

    /**
     * Override this method in your own class to customize onboarding steps
     */
    protected function getWizardSteps(): array
    {
        return [
            Step::make('profile')
                ->label('Complete Your Profile')
                ->icon('heroicon-o-user')
                ->description('Just a few details to get started')
                ->schema([
                    Section::make()
                        ->schema([
                            TextInput::make('name')
                                ->label('Full Name')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->label('Email Address')
                                ->email()
                                ->required()
                                ->disabled(),
                        ])
                        ->columns(2),
                ]),

            Wizard\Step::make('complete')
                ->label('All Set!')
                ->icon('heroicon-o-check-circle')
                ->description('You\'re ready to go')
                ->schema([
                    Section::make()
                        ->schema([
                            ViewField::make('completion')
                                ->view('filament-onboarding::components.completion-message')
                                ->columnSpanFull()
                        ]),
                ]),
        ];
    }

    /**
     * Override this method to add custom logic after onboarding
     */
    public function submit(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        try {
            // Update user data
            $user->update([
                'name' => $data['name'],
            ]);

            // Mark as onboarded
            $this->onboarded();

            // Success notification
            Notification::make()
                ->success()
                ->title('Onboarding Complete!')
                ->body('Welcome aboard!')
                ->send();

            // Redirect
            $this->redirect($this->getRedirectUrl());
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Something went wrong: ' . $e->getMessage())
                ->send();
        }
    }

    /**
     * Mark user as onboarded
     */
    protected function onboarded(): void
    {
        Auth::user()->markOnboardingAsComplete();
    }

    /**
     * Check if user can skip onboarding
     */
    protected function canSkip(): bool
    {
        return config('filament-onboarding.allow_skipping', false);
    }

    /**
     * Get redirect URL after completion
     */
    protected function getRedirectUrl(): string
    {
        return config('filament-onboarding.redirect.after_completion') ?? filament()->getUrl();
    }

    /**
     * Skip onboarding (if allowed)
     */
    public function skipOnboarding(): void
    {
        if (! $this->canSkip()) {
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
