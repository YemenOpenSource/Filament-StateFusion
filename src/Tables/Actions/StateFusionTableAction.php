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
use Filament\Tables\Actions\Action;

class StateFusionTableAction extends Action implements HasStateAttributesContract, HasStateFusionAction
{
    use HasStateAttributes;
    use InteractsWithStateAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->label(fn () => is_a($this->getClassInstance(), HasLabel::class) ? $this->getClassInstance()?->getLabel() : (is_a($this->getToStateClass(), HasLabel::class) ? $this->getToStateClass()?->getLabel() : null));
        $this->color(fn () => is_a($this->getClassInstance(), HasColor::class) ? $this->getClassInstance()?->getColor() : (is_a($this->getToStateClass(), HasColor::class) ? $this->getToStateClass()?->getColor() : null));
        $this->icon(fn () => is_a($this->getClassInstance(), HasIcon::class) ? $this->getClassInstance()?->getIcon() : ((is_a($this->getToStateClass(), HasIcon::class)) ? $this->getToStateClass()?->getIcon() : null));
        $this->tooltip(fn () => is_a($this->getClassInstance(), HasDescription::class) ? $this->getClassInstance()?->getDescription() : (is_a($this->getToState(), HasDescription::class) ? $this->getToState()?->getDescription() : null));
        $this->hidden(
            function ($record) {
                return ! in_array($this->getToState()::getMorphClass(), $record->{$this->getAttribute()}->transitionableStates());

                // return ! ($this->getFromState()::config()->isTransitionAllowed($this->getFromState()::getMorphClass(), $this->getToState()::getMorphClass()) && $record->{$this->getAttribute()}?->canTransitionTo($this->getToStateClass()));
            }
        );
        $this->action(function ($record, array $data): void {
            if (empty($data)) {
                $record->{$this->getAttribute()}->transitionTo($this->getToStateClass());
            } else {
                $record->{$this->getAttribute()}->transitionTo($this->getToStateClass(), $data[array_key_first($data)]);
            }
            $this->success();
        });

        $this->form(fn () => ((method_exists($this->getClassInstance(), 'form')) ? $this->getClassInstance()->form() : null));
        $this->modalDescription(fn () => is_a($this->getClassInstance(), HasDescription::class) ? $this->getClassInstance()->getDescription() : (is_a($this->getToState(), HasDescription::class) ? $this->getToState()->getDescription() : null));
        $this->modalIcon(fn () => $this->getIcon());
        $this->modalIconColor(fn () => $this->getColor());
        $this->requiresConfirmation();
    }
}
