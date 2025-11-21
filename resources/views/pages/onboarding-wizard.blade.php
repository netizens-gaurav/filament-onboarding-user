<x-filament-panels::page>
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-4xl">
                Welcome to {{ config('app.name') }}!
            </h1>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                Let's get your account set up in just a few steps
            </p>
        </div>

        <x-filament-panels::form wire:submit="submit">
            {{ $this->form }}
        </x-filament-panels::form>

        @if(config('filament-onboarding.allow_skipping'))
            <div class="mt-4 text-center">
                <button
                    type="button"
                    wire:click="skipOnboarding"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                >
                    Skip for now
                </button>
            </div>
        @endif
    </div>
</x-filament-panels::page>
