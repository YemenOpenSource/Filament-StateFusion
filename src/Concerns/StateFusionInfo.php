<?php

namespace A909M\FilamentStateFusion\Concerns;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
trait StateFusionInfo
{
    /**
     * getStatesLabel
     *
     * @param  TModel  $model
     */
    public static function getStatesLabel($model): array
    {
        return self::getStateMapping()->mapWithKeys(function ($stateClass) use ($model) {
            return [$stateClass::$name => (new $stateClass($model))->getLabel()];
        })->toArray();
    }

    /**
     * getStatesColor
     *
     * @param  TModel  $model
     */
    public static function getStatesColor($model): array
    {
        return self::getStateMapping()->mapWithKeys(function ($stateClass) use ($model) {
            return [$stateClass::$name => (new $stateClass($model))->getColor()];
        })->toArray();
    }

    /**
     * getStatesDescription
     *
     * @param  TModel  $model
     */
    public static function getStatesDescription($model): array
    {
        return self::getStateMapping()->mapWithKeys(function ($stateClass) use ($model) {
            return [$stateClass::$name => (new $stateClass($model))->getDescription()];
        })->toArray();
    }

    /**
     * getStatesIcon
     *
     * @param  TModel  $model
     */
    public static function getStatesIcon($model): array
    {
        return self::getStateMapping()->mapWithKeys(function ($stateClass) use ($model) {
            return [$stateClass::$name => (new $stateClass($model))->getIcon()];
        })->toArray();
    }
}
