<?php

namespace Netizensgaurav\FilamentOnboarding;

use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Netizensgaurav\FilamentOnboarding\Commands\FilamentOnboardingCommand;
use Netizensgaurav\FilamentOnboarding\Testing\TestsFilamentOnboarding;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentOnboardingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-onboarding-user';

    public static string $viewNamespace = 'filament-onboarding';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('netizens-gaurav/filament-onboarding-user');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(FilamentOnboarding::class, function () {
            return new FilamentOnboarding;
        });
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-onboarding/{$file->getFilename()}"),
                ], 'filament-onboarding-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsFilamentOnboarding);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'netizens-gaurav/filament-onboarding-user';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        $assets = [];

        $cssPath = __DIR__ . '/../resources/dist/filament-onboarding.css';
        $jsPath = __DIR__ . '/../resources/dist/filament-onboarding.js';

        // Only register assets if they exist
        if (file_exists($cssPath)) {
            $assets[] = Css::make('filament-onboarding-styles', $cssPath);
        }

        if (file_exists($jsPath)) {
            $assets[] = Js::make('filament-onboarding-scripts', $jsPath);
        }

        return $assets;
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentOnboardingCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_onboarding_steps_table',
            'create_user_onboarding_progress_table',
            'add_onboarding_columns_to_users_table',
        ];
    }
}
