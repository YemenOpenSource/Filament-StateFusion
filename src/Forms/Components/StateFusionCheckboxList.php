<?php

namespace A909M\FilamentStateFusion\Forms\Components;

use A909M\FilamentStateFusion\Concerns\HasStateAttributes;
use A909M\FilamentStateFusion\Contracts\HasStateAttributesContract;
use Filament\Forms\Components\CheckboxList;

class StateFusionCheckboxList extends CheckboxList implements HasStateAttributesContract
{
    use HasStateAttributes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->options(fn ($model) => (new $model)->getCasts()[$this->getAttribute()]::getStatesLabel($model));
        $this->descriptions(fn ($model) => (new $model)->getCasts()[$this->getAttribute()]::getStatesDescription($model));
    }
}
