<?php

namespace Netizensgaurav\FilamentOnboarding\Commands;

use Illuminate\Console\Command;

class FilamentOnboardingCommand extends Command
{
    public $signature = 'filament-onboarding';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
