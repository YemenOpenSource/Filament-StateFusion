<?php

namespace A909M\FilamentStateFusion\Tables\Actions;

use A909M\FilamentStateFusion\Concerns\HasStateAttributes;
use A909M\FilamentStateFusion\Concerns\InteractsWithStateAction;
use A909M\FilamentStateFusion\Contracts\HasStateAttributesContract;
use A909M\FilamentStateFusion\Contracts\HasStateFusionAction;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Spatie\ModelStates\State;

class StateFusionBulkAction extends BulkAction implements HasStateAttributesContract, HasStateFusionAction
{
    use HasStateAttributes;
    use InteractsWithStateAction;

    public string | State | null $fromState = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->label(fn () => is_a($this->getClassInstance(), HasLabel::class) ? $this->getClassInstance()?->getLabel() : (is_a($this->getToStateClass(), HasLabel::class) ? $this->getToStateClass()?->getLabel() : null));
        $this->color(fn () => is_a($this->getClassInstance(), HasColor::class) ? $this->getClassInstance()?->getColor() : (is_a($this->getToStateClass(), HasColor::class) ? $this->getToStateClass()?->getColor() : null));
        $this->icon(fn () => is_a($this->getClassInstance(), HasIcon::class) ? $this->getClassInstance()?->getIcon() : ((is_a($this->getToStateClass(), HasIcon::class)) ? $this->getToStateClass()?->getIcon() : null));
        $this->tooltip(fn () => is_a($this->getClassInstance(), HasDescription::class) ? $this->getClassInstance()?->getDescription() : (is_a($this->getToState(), HasDescription::class) ? $this->getToState()?->getDescription() : null));
        $this->action(function (Collection $records, $data) {
            if (empty($data)) {
                $records->each(fn ($record) => ($record->{$this->getAttribute()}?->equals($this->getFromState())
                    && in_array($this->getToState()::getMorphClass(), $record->{$this->getAttribute()}->transitionableStates())) ? $record->{$this->getAttribute()}->transitionTo($this->getToStateClass()) : null);
            } else {
                $records->each(fn ($record) => ($record->{$this->getAttribute()}?->equals($this->getFromState())
                    && in_array($this->getToState()::getMorphClass(), $record->{$this->getAttribute()}->transitionableStates())) ? $record->{$this->getAttribute()}->transitionTo($this->getToStateClass(), $data[array_key_first($data)]) : null);
            }
        });

        $this->form(fn () => ((method_exists($this->getClassInstance(), 'form')) ? $this->getClassInstance()->form() : null));
        $this->modalDescription(fn () => is_a($this->getClassInstance(), HasDescription::class) ? $this->getClassInstance()->getDescription() : (is_a($this->getToState(), HasDescription::class) ? $this->getToState()->getDescription() : null));
        $this->modalIcon(fn () => $this->getIcon());
        $this->modalIconColor(fn () => $this->getColor());
        $this->requiresConfirmation();
    }

    public function transition(string | State | null $fromState, string | State | null $toState): self
    {
        $this->toState = $toState;
        $this->fromState = $fromState;

        return $this;
    }

    public function getFromState(): string | State | null
    {
        return $this->evaluate($this->fromState);
    }
}
