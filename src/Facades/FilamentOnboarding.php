<?php

namespace Netizensgaurav\FilamentOnboarding\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Netizensgaurav\FilamentOnboarding\FilamentOnboarding
 */
class FilamentOnboarding extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Netizensgaurav\FilamentOnboarding\FilamentOnboarding::class;
    }
}
