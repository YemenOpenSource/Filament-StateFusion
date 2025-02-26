<?php

namespace A909M\FilamentStateFusion\Tables\Columns;

use A909M\FilamentStateFusion\Concerns\HasStateAttributes;
use A909M\FilamentStateFusion\Contracts\HasStateAttributesContract;
use Filament\Tables\Columns\SelectColumn;

class StateFusionSelectColumn extends SelectColumn implements HasStateAttributesContract
{
    use HasStateAttributes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->selectablePlaceholder(false);
        $this->options(fn ($model) => (new $model)->getCasts()[$this->getAttribute()]::getStatesLabel($model));
        $this->disableOptionWhen(
            function (string $value, $record, $state) {
                return ($state == $value) ? false : ! in_array($value, $record->{$this->getAttribute()}->transitionableStates());
            }
        );
    }
}
