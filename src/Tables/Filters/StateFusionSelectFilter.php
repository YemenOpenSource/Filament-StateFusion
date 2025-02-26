<?php

namespace A909M\FilamentStateFusion\Tables\Filters;

use A909M\FilamentStateFusion\Concerns\HasStateAttributes;
use A909M\FilamentStateFusion\Contracts\HasStateAttributesContract;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StateFusionSelectFilter extends SelectFilter implements HasStateAttributesContract
{
    use HasStateAttributes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->options(fn (Table $table) => (new ($table->getModel()))->getCasts()[$this->getAttribute()]::getStatesLabel($table->getModel()));
    }
}
