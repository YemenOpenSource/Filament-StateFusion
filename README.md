# Filament-StateFusion

**Filament StateFusion** is a powerful FilamentPHP plugin that seamlessly integrates [Spatie Laravel Model States](https://spatie.be/docs/laravel-model-states) into the Filament admin panel. This package provides an intuitive way to manage model states, transitions, and filtering within Filament, enhancing the user experience and developer productivity.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/a909m/filament-statefusion.svg?style=flat-square)](https://packagist.org/packages/a909m/filament-statefusion)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/a909m/filament-statefusion/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/a909m/filament-statefusion/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/a909m/filament-statefusion/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/a909m/filament-statefusion/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/a909m/filament-statefusion.svg?style=flat-square)](https://packagist.org/packages/a909m/filament-statefusion)

# Features

-   Listing states within tables and exports
-   Filtering records by states
-   Grouping records by states
-   Transitioning to valid states using select or toggle button components
-   Transitioning to valid states using page and table actions
-   Bulk transition to valid states using bulk actions
-   Out-of-the-box support for the Spatie Laravel Model States package
-   Compatible with dark mode

## Installation

You can install the package via composer:

```bash
composer require a909m/filament-statefusion
```

## Setup

In this paragraph, we list the steps you need to follow to get up and running with the out-of-the-box supported Spatie
integration.

### Spatie

Make sure you have configured at least one Spatie Laravel model state. For more information, refer to the official
Spatie [documentation](https://spatie.be/docs/laravel-model-states/v2/01-introduction).

#### State Preparation

When utilizing Spatie Laravel Model States, you'll have several abstract state classes. These abstract classes
require certain modifications. To properly integrate them, it's necessary to implement the `FilamentSpatieState`
interface and utilize the `ProvidesSpatieStateToFilament` trait.

Here's an example of the `OrderState` abstract class with the necessary modifications already applied.

```php
<?php

namespace App\States;

use App\Models\Order;
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

> [!TIP]
> More information about state configuration can be found in the official
> Spatie [documentation](https://spatie.be/docs/laravel-model-states/v2/working-with-states/01-configuring-states).

#### Transition Preparation

Spatie Laravel model states offer support for custom transition classes. All custom transition classes must implement
the `HasFilamentStateFusion` interface and use the `StateFusionInfo` trait before they can be used
within Filament.

Here is an example of the `ToCancelled` transition class with the necessary modifications in place.

```php
<?php

namespace App\States;

use App\Models\Order;
use Spatie\ModelStates\Transition;


final class ToCancelled extends Transition
{

    public function __construct(
        private readonly Order $order,
    ) {
    }

    public function handle(): Order
    {
        $this->order->state = new CancelledState($this->order);
        $this->order->cancelled_at = now();

        $this->order->save();

        return $this->order;
    }
}
```

> [!TIP]
> For more information about transition configuration, refer to the official Spatie
> [documentation](https://spatie.be/docs/laravel-model-states/v2/working-with-transitions/02-custom-transition-classes).

##### Additional Transition Data

Most of the time, additional data is needed before transitioning to a new state. Considering the `ToCancelled`
transition, it would be beneficial to store a reason explaining why the state transitioned to cancelled state. By adding
a `form` method to the transition class, a form will be displayed when initiating the transition.

Here is an example `ToCancelled` transition class with the form is place. This transition will display a reason
textarea when the `StateFusionAction` or `StateFusionTableAction` button is clicked.

```php
<?php

namespace App\States;

use App\Models\Order;
use Spatie\ModelStates\Transition;


final class ToCancelled extends Transition 
{

    public function __construct(
        private readonly Order $order,
        private readonly string $reason = '',
    ) {
    }

    public function handle(): Order
    {
        $this->order->state = new CancelledState($this->order);
        $this->order->cancelled_at = now();
        $this->order->cancellation_reason = $this->reason;

        $this->order->save();

        return $this->order;
    }

    public function form(): array | Closure | null
    {
        return [
            Textarea::make('reason')
                ->required()
                ->minLength(1)
                ->maxLength(1000)
                ->rows(5)
                ->helperText(__('This reason will be sent to the customer.')),
        ];
    }
}
```

> [!WARNING]
> Since the plug-in needs to create transition instances to determine if there is a form, all constructor properties,
> except for the model, must have default values.

By default, this plug-in will map the form component names to their constructor property names. Considering the
previous `ToCancelled` transition, the `reason` textarea input will correspond to the constructor property `$reason`. If
you want to make any modifications before creating the transition instance, you can override the static method `fill`.

For example, you can prefix the `reason`:

```php
<?php

namespace App\States;

use App\Models\Order;
use Illuminate\Support\Arr;
use Spatie\ModelStates\Transition;


final class ToCancelled extends Transition i
{

    public function __construct(
        private readonly Order $order,
        private readonly string $reason = '',
    ) {
    }

    public static function fill(Model $model, array $formData): SpatieTransition
    {
        return new self(
            order: $model,
            reason: 'The order is cancelled because: ' . Arr::get($formData, 'reason'),
        );
    }

    public function handle(): Order
    {
        $this->order->state = new CancelledState($this->order);
        $this->order->cancelled_at = now();
        $this->order->cancellation_reason = $this->reason;

        $this->order->save();

        return $this->order;
    }

    public function form(): array | Closure | null
    {
        return [
            Textarea::make('reason')
                ->required()
                ->minLength(1)
                ->maxLength(1000)
                ->rows(5)
                ->helperText(__('This reason will be sent to the customer.')),
        ];
    }
}
```

#### Optional Label, Description, Color and Icon

By default, the name of the state class is used as a label (for example, `CancelledState` will have the
label `Cancelled`), without any assigned description, color or icon. If you desire a different label, description,
color, or icon, you must implement the `HasLabel`, `HasDescription`, `HasColor`, or `HasIcon` interface.

Here is an example of the `Cancelled` state with `HasLabel`, `HasDescription`, `HasColor`, and `HasIcon` implemented.

```php
<?php

namespace App\States;

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

> [!NOTE]
> The description is used when utilizing the `StateFusionRadio` component.

By default, "Transition to" followed by the name of the destination state is used as the transition label. Like states,
it has no color or icon. If you want a different label, or if you want to use a color or icon; you have to implement
the `HasLabel`, `HasColor` or `HasIcon` interface.

Here is an example `ToCancelled` transtition with `HasLabel`, `HasColor` and `HasIcon` implemented.

```php
<?php

namespace App\States;

use App\Models\Order;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Spatie\ModelStates\Transition;


final class ToCancelled extends Transition implements HasLabel, HasColor, HasIcon
{


    public function __construct(
        private readonly Order $order,
        private readonly string $reason = '',
    ) {
    }

    public function handle(): Order
    {
        $this->order->state = new CancelledState($this->order);
        $this->order->cancelled_at = now();
        $this->order->cancellation_reason = $this->reason;

        $this->order->save();

        return $this->order;
    }

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

    public function form(): array | Closure | null
    {
        return [
            Textarea::make('reason')
                ->required()
                ->minLength(1)
                ->maxLength(1000)
                ->rows(5)
                ->helperText(__('This reason will be sent to the customer.')),
        ];
    }
}
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

-   [A909M](https://github.com/A909M)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
