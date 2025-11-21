# A comprehensive onboarding solution for Filament applications with support for both single-tenant and multi-tenant SaaS platforms

[![Latest Version on Packagist](https://img.shields.io/packagist/v/netizens-gaurav/filament-onboarding.svg?style=flat-square)](https://packagist.org/packages/netizens-gaurav/filament-onboarding)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/netizens-gaurav/filament-onboarding/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/netizens-gaurav/filament-onboarding/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/netizens-gaurav/filament-onboarding/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/netizens-gaurav/filament-onboarding/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/netizens-gaurav/filament-onboarding.svg?style=flat-square)](https://packagist.org/packages/netizens-gaurav/filament-onboarding)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require netizens-gaurav/filament-onboarding
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-onboarding-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-onboarding-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-onboarding-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filamentOnboarding = new Netizensgaurav\FilamentOnboarding();
echo $filamentOnboarding->echoPhrase('Hello, Netizensgaurav!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Gaurav Vaishnav](https://github.com/netizens-gaurav)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
