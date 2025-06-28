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
            return [$stateClass::$name => (new $stateClass($model))->getLabel() ?? $stateClass::$name];
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
            $instance = new $stateClass($model);
            // Check if the class implements HasColor interface before calling getColor
            if (method_exists($instance, 'getColor')) {
                return [$stateClass::$name => $instance->getColor()];
            }

            return [$stateClass::$name => null];
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
            $instance = new $stateClass($model);
            // Check if the class implements HasDescription interface before calling getDescription
            if (method_exists($instance, 'getDescription')) {
                return [$stateClass::$name => $instance->getDescription()];
            }

            return [$stateClass::$name => null];
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
            $instance = new $stateClass($model);
            // Check if the class implements HasIcon interface before calling getIcon
            if (method_exists($instance, 'getIcon')) {
                return [$stateClass::$name => $instance->getIcon()];
            }

            return [$stateClass::$name => null];
        })->toArray();
    }
}
