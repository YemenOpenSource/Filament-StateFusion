# Filament-StateFusion

[![Latest Version on Packagist](https://img.shields.io/packagist/v/a909m/filament-statefusion.svg?style=flat-square)](https://packagist.org/packages/a909m/filament-statefusion)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/a909m/filament-statefusion/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/a909m/filament-statefusion/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/a909m/filament-statefusion/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/a909m/filament-statefusion/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/a909m/filament-statefusion.svg?style=flat-square)](https://packagist.org/packages/a909m/filament-statefusion)

**Filament StateFusion** is a powerful [FilamentPHP](https://filamentphp.com/) plugin that seamlessly integrates [Spatie Laravel Model States](https://spatie.be/docs/laravel-model-states) into the Filament admin panel. Effortlessly manage model states, transitions, and filtering within Filament, enhancing both user experience and developer productivity.

---

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Getting Started](#getting-started)
- [Quickstart](#quickstart)
- [Screenshots](#screenshots)
- [Installation](#installation)
- [Setup](#setup)
- [Usage](#usage)
- [Customization](#customization)
- [Testing](#testing)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Security](#security-vulnerabilities)
- [Credits](#credits)
- [License](#license)

---

## Introduction

Filament-StateFusion brings the power of [Spatie Laravel Model States](https://spatie.be/docs/laravel-model-states) to your Filament admin panel. It allows you to:

- Display model states in tables
- Filter and group records by state
- Transition between states using intuitive UI components
- Support custom transitions with forms and additional data

This plugin is ideal for applications that use state machines, such as order processing, publishing workflows, or any scenario where models have well-defined states and transitions.

---

## Features

- List model states in Filament tables
- Filter and group records by state
- Transition to valid states using select, toggle, page, or table actions
- Bulk transition records to new states
- Out-of-the-box support for [Spatie Laravel Model States](https://spatie.be.docs/laravel-model-states)
- Custom transition forms for collecting additional data
- Customizable labels, colors, icons, and descriptions for states and transitions
- Compatible with Filament dark mode

---

## Requirements

This plugin is designed to work with the following dependencies:

- PHP: ^8.1
- Laravel: ^10.0|^11.0
- Filament: ^3.0
- Spatie Laravel Model States: ^2.0


## Getting Started

First, you need to have the [Spatie Laravel Model States](https://spatie.be/docs/laravel-model-states) package installed and configured. Make sure you have created an abstract state class for your model.

Next, install the Filament-StateFusion plugin via Composer:

```bash
composer require a909m/filament-statefusion
```

Then, implement the `HasFilamentStateFusion` interface and use the `StateFusionInfo` trait on your abstract state class.

Finally, you can start using the components and actions provided by this plugin in your Filament resources.

---

## Quickstart

Here\'s a quick example of how to get started.

1.  **Define your states and transitions:**

    ```php
    // app/Models/States/OrderState.php
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
                ->allowTransition(ProcessingState::class, ShippedState::class);
        }
    }
    ```

2.  **Add the state to your model:**

    ```php
    // app/Models/Order.php
    use App\Models\States\OrderState;
    use Illuminate\Database\Eloquent\Model;
    use Spatie\ModelStates\HasStates;

    class Order extends Model
    {
        use HasStates;

        protected $casts = [
            \'status\' => OrderState::class,
        ];
    }
    ```

3.  **Use the components in your Filament resource:**

    ```php
    // app/Filament/Resources/OrderResource.php
    use A909M\FilamentStateFusion\Tables\Columns\StateFusionSelectColumn;
    use A909M\FilamentStateFusion\Tables\Filters\StateFusionSelectFilter;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables\Table;

    class OrderResource extends Resource
    {
        // ...

        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    StateFusionSelectColumn::make(\'status\'),
                ])
                ->filters([
                    StateFusionSelectFilter::make(\'status\'),
                ]);
        }
    }
    ```

---

## Screenshots

| StateFusionSelectFilter | StateFusionSelectColumn | StateFusionToggleButtons |
| :---: | :---: | :---: |
| ![StateFusionSelectFilter](docs/screenshots/StateFusionSelectFilter.png) | ![StateFusionSelectColumn](docs/screenshots/StateFusionSelectColumn.png) | ![StateFusionToggleButtons](docs/screenshots/StateFusionToggleButtons.png) |

| StateFusionTableAction | StateFusionBulkAction | StateFusionRadio |
| :---: | :---: | :---: |
| ![StateFusionTableAction](docs/screenshots/StateFusionTableAction.png) | ![StateFusionBulkAction](docs/screenshots/StateFusionBulkAction.png) | ![StateFusionRadio](docs/screenshots/StateFusionRadio.png) |

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

### Form Components

You can use the following state-aware components in your forms:

- `StateFusionSelect`
- `StateFusionCheckboxList`
- `StateFusionRadio`
- `StateFusionToggleButtons`

```php
use A909M\FilamentStateFusion\Forms\Components\StateFusionSelect;
use A909M\FilamentStateFusion\Forms\Components\StateFusionCheckboxList;
use A909M\FilamentStateFusion\Forms\Components\StateFusionRadio;
use A909M\FilamentStateFusion\Forms\Components\StateFusionToggleButtons;

StateFusionSelect::make('status'),
StateFusionCheckboxList::make('status'),
StateFusionRadio::make('status'),
StateFusionToggleButtons::make('status'),
```

### Table Columns

#### StateFusionSelectColumn
Display state information in your Filament tables and allow for quick state transitions.

```php
use A909M\FilamentStateFusion\Tables\Columns\StateFusionSelectColumn;

StateFusionSelectColumn::make('status')
    ->sortable()
    ->toggleable();
```

#### TextColumn
You can also use the standard `TextColumn` to display the state as a badge. If your state classes implement `HasColor` and `HasIcon`, the badge will automatically reflect the state's color, and you can display an icon.

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('status')
    ->badge()
    ->icon(fn ($record) => $record->status->getIcon());
```

### Table Filter

Filter records by state:

```php
use A909M\FilamentStateFusion\Tables\Filters\StateFusionSelectFilter;

StateFusionSelectFilter::make('status');
```

### Table Actions

#### StateFusionTableAction
Add state transition actions to your table rows.

```php
use A909M\FilamentStateFusion\Tables\Actions\StateFusionTableAction;

StateFusionTableAction::make('approve')
    ->fromState(PendingState::class)
    ->toState(ApprovedState::class);
```

#### StateFusionBulkAction
Transition multiple records at once.

```php
use A909M\FilamentStateFusion\Tables\Actions\StateFusionBulkAction;

StateFusionBulkAction::make('approve')
    ->fromState(PendingState::class)
    ->toState(ApprovedState::class);
```

### Infolist Entries

#### TextEntry
Similar to the table column, you can use the standard `TextEntry` in your infolists to display the model state as a badge with an icon and color.

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('status')
    ->badge()
    ->icon(fn ($record) => $record->status->getIcon());
```

### Page Actions

#### StateFusionAction
Create actions to transition between states from a page.

```php
use A909M\FilamentStateFusion\Actions\StateFusionAction;

StateFusionAction::make('approve')
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

- [A909M](https://github.com/a909m)
- [All Contributors](../../contributors)

---

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
