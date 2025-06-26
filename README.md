# Filament-StateFusion

[![Latest Version on Packagist](https://img.shields.io/packagist/v/a909m/filament-statefusion.svg?style=flat-square)](https://packagist.org/packages/a909m/filament-statefusion)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/a909m/filament-statefusion/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/a909m/filament-statefusion/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/a909m/filament-statefusion/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/a909m/filament-statefusion/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/a909m/filament-statefusion.svg?style=flat-square)](https://packagist.org/packages/a909m/filament-statefusion)

**Filament StateFusion** is a powerful [FilamentPHP](https://filamentphp.com/) plugin that seamlessly integrates [Spatie Laravel Model States](https://spatie.be/docs/laravel-model-states) into the Filament admin panel. Effortlessly manage model states, transitions, and filtering within Filament, enhancing both user experience and developer productivity.

---

## Table of Contents

-   [Introduction](#introduction)
-   [Features](#features)
-   [Screenshots](#screenshots)
-   [Installation](#installation)
-   [Setup](#setup)
-   [Usage](#usage)
-   [Customization](#customization)
-   [Testing](#testing)
-   [Changelog](#changelog)
-   [Contributing](#contributing)
-   [Security](#security-vulnerabilities)
-   [Credits](#credits)
-   [License](#license)

---

## Introduction

Filament-StateFusion brings the power of [Spatie Laravel Model States](https://spatie.be/docs/laravel-model-states) to your Filament admin panel. It allows you to:

-   Display model states in tables and exports
-   Filter and group records by state
-   Transition between states using intuitive UI components
-   Support custom transitions with forms and additional data

This plugin is ideal for applications that use state machines, such as order processing, publishing workflows, or any scenario where models have well-defined states and transitions.

---

## Features

-   List model states in Filament tables
-   Filter and group records by state
-   Transition to valid states using select, toggle, page, or table actions
-   Bulk transition records to new states
-   Out-of-the-box support for [Spatie Laravel Model States](https://spatie.be.docs/laravel-model-states)
-   Custom transition forms for collecting additional data
-   Customizable labels, colors, icons, and descriptions for states and transitions
-   Compatible with Filament dark mode

---

## Installation

You can install the package via Composer:

```bash
composer require a909m/filament-statefusion
```

---

## Setup

1. **Prepare your abstract state class:**

    Implement the `HasFilamentStateFusion` interface and use the `StateFusionInfo` trait on your abstract state class (not the Eloquent model):

    ```php
    use A909M\FilamentStateFusion\Concerns\StateFusionInfo;
    use A909M\FilamentStateFusion\Contracts\HasFilamentStateFusion;
    use Spatie\ModelStates\State;
    use Spatie\ModelStates\StateConfig;

    abstract class OrderState extends State implements HasFilamentStateFusion
    {
        use StateFusionInfo;

        public static function config(): StateConfig
        {
            return parent::config()
                ->default(NewState::class)
                ->allowTransition(NewState::class, ProcessingState::class)
                ->allowTransition(ProcessingState::class, ShippedState::class)
                ->allowTransition(ShippedState::class, DeliveredState::class)
                ->allowTransition([NewState::class, ProcessingState::class], CancelledState::class, ToCancelled::class);
        }
    }
    ```

2. **Custom transitions:**

    If you use custom transition classes, implement `HasFilamentStateFusion` and use `StateFusionInfo` on them as well.

    ```php
    use A909M\FilamentStateFusion\Concerns\StateFusionInfo;
    use A909M\FilamentStateFusion\Contracts\HasFilamentStateFusion;
    use Spatie\ModelStates\Transition;

    final class ToCancelled extends Transition implements HasFilamentStateFusion
    {
        use StateFusionInfo;

        // ... constructor and handle method ...
    }
    ```

---

## Usage

### Table Column

Display state information in your Filament tables:

```php
use A909M\FilamentStateFusion\Tables\Columns\StateFusionSelectColumn;

StateFusionSelectColumn::make('status')
    ->sortable()
    ->toggleable();
```

### Table Filter

Filter records by state:

```php
use A909M\FilamentStateFusion\Tables\Filters\StateFusionSelectFilter;

StateFusionSelectFilter::make('status');
```

### State Transitions

Create actions to transition between states:

```php
use A909M\FilamentStateFusion\Actions\StateFusionAction;

StateFusionAction::make('approve')
    ->fromState(PendingState::class)
    ->toState(ApprovedState::class);
```

### Bulk Actions

Transition multiple records at once:

```php
use A909M\FilamentStateFusion\Tables\Actions\StateFusionBulkAction;

StateFusionBulkAction::make('approve')
    ->fromState(PendingState::class)
    ->toState(ApprovedState::class);
```

### Table Actions

Add state transition actions to your tables:

```php
use A909M\FilamentStateFusion\Tables\Actions\StateFusionTableAction;

StateFusionTableAction::make('approve')
    ->fromState(PendingState::class)
    ->toState(ApprovedState::class);
```

---

## Customization

### Customizing States

To customize how states appear in the UI, implement the `HasLabel`, `HasDescription`, `HasColor`, or `HasIcon` interfaces on your **concrete state classes**:

```php
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

final class CancelledState extends OrderState implements HasDescription, HasColor, HasIcon, HasLabel
{
    public function getLabel(): string
    {
        return __('Order Cancelled');
    }

    public function getColor(): array
    {
        return Color::Red;
    }

    public function getIcon(): string
    {
        return 'heroicon-o-x-circle';
    }

    public function getDescription(): ?string
    {
        return 'Order cancelled, transaction reversed.';
    }
}
```

### Customizing Transitions

Similarly, transitions can be customized by implementing the same interfaces:

```php
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Spatie\ModelStates\Transition;

final class ToCancelled extends Transition implements HasLabel, HasColor, HasIcon
{
    public function getLabel(): string
    {
        return __('Mark as Cancelled');
    }

    public function getColor(): array
    {
        return Color::Red;
    }

    public function getIcon(): string
    {
        return 'heroicon-o-x-circle';
    }
}
```

---

## Testing

```bash
composer test
```

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details on how to contribute to this project.

---

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

---

## Credits

-   [A909M](https://github.com/a909m)
-   [Spatie Laravel Model States](https://spatie.be/docs/laravel-model-states)
-   [All Contributors](../../contributors)

---

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
