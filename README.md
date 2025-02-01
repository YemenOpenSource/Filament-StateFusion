# Filament StateFusion is a powerful FilamentPHP plugin that seamlessly integrates Spatie Laravel Model States into the Filament admin panel. This package provides an intuitive way to manage model states, transitions, and filtering within Filament, enhancing the user experience and developer productivity.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/a909m/filament-statefusion.svg?style=flat-square)](https://packagist.org/packages/a909m/filament-statefusion)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/a909m/filament-statefusion/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/a909m/filament-statefusion/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/a909m/filament-statefusion/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/a909m/filament-statefusion/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/a909m/filament-statefusion.svg?style=flat-square)](https://packagist.org/packages/a909m/filament-statefusion)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require a909m/filament-statefusion
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-statefusion-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-statefusion-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-statefusion-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filamentStateFusion = new A909M\FilamentStateFusion();
echo $filamentStateFusion->echoPhrase('Hello, A909M!');
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

- [A909M](https://github.com/A909M)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
