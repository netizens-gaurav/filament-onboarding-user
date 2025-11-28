<?php

namespace Netizensgaurav\FilamentOnboarding\Commands;

use Illuminate\Console\Command;

class FilamentOnboardingCommand extends Command
{
    public $signature = 'filament-onboarding';

    public $description = 'Install Filament Onboarding plugin';

    public function handle(): int
    {
        $this->info('Installing Filament Onboarding...');

        // Publish config
        $this->call('vendor:publish', [
            '--tag' => 'filament-onboarding-config',
            '--force' => true,
        ]);

        // Publish migrations
        $this->call('vendor:publish', [
            '--tag' => 'filament-onboarding-migrations',
            '--force' => true,
        ]);

        // Run migrations
        if ($this->confirm('Would you like to run the migrations now?', true)) {
            $this->call('migrate');
        }

        // Seed onboarding steps
        if ($this->confirm('Would you like to seed default onboarding steps?', true)) {
            $this->call('filament-onboarding:seed');
        }

        $this->info('✅ Filament Onboarding installed successfully!');
        $this->newLine();
        $this->info('Next steps:');
        $this->line('1. Add HasOnboarding trait to your User model');
        $this->line('2. Register the plugin in your PanelProvider');
        $this->line('3. Configure the plugin in config/onboarding.php');

        return self::SUCCESS;
    }
}
