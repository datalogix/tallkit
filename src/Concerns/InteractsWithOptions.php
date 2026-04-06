<?php

namespace TALLKit\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\View\ComponentAttributeBag;

trait InteractsWithOptions
{
    public function parseOptions(ComponentAttributeBag $attributes)
    {
        $options = $attributes->pluck('options');
        $optionValue = $attributes->pluck('option-value');
        $optionLabel = $attributes->pluck('option-label');
        $optionGroupLabel = $attributes->pluck('option-group-label');
        $optionGroupChildren = $attributes->pluck('option-group-children');
        $optionGroupChildrenValue = $attributes->pluck('option-group-children-value');
        $optionGroupChildrenLabel = $attributes->pluck('option-group-children-label');

        if (is_subclass_of($options, \BackedEnum::class) || is_subclass_of($options, \UnitEnum::class)) {
            return Arr::mapWithKeys($options::cases(), fn ($enum) => [
                $enum->value => method_exists($enum, 'label') ? $enum->label() : $enum->name,
            ]);
        }

        if (is_string($options) && class_exists($options)) {
            $options = app($options);
        }

        if ($options instanceof Model || $options instanceof Builder) {
            $options = $options->get();
        }

        $useValueAsKey = is_array($options) && array_keys($options) === range(0, count($options) - 1);

        return collect($options)->mapWithKeys(function ($value, $key) use ($useValueAsKey, $optionValue, $optionLabel, $optionGroupLabel, $optionGroupChildren, $optionGroupChildrenValue, $optionGroupChildrenLabel) {
            $_optionValue = data_get($value, $optionValue ?? 'id') ?? ($useValueAsKey ? $value : $key);
            $_optionLabel = data_get($value, $optionLabel ?? 'name', $value);

            if (! $optionGroupChildren && ! is_array($_optionLabel)) {
                return [$_optionValue => $_optionLabel];
            }

            $_optionGroupLabel = data_get($value, $optionGroupLabel ?? $optionLabel ?? 'name', $key);
            $_optionGroupChildren = collect(data_get($value, $optionGroupChildren));

            if ($_optionGroupChildren->isEmpty()) {
                return [];
            }

            return [
                $_optionGroupLabel => $_optionGroupChildren->mapWithKeys(function ($value, $key) use ($optionGroupChildrenValue, $optionGroupChildrenLabel, $optionValue, $optionLabel) {
                    $_optionValue = data_get($value, $optionGroupChildrenValue ?? $optionValue ?? 'id', $key);
                    $_optionLabel = data_get($value, $optionGroupChildrenLabel ?? $optionLabel ?? 'name', $value);

                    return [$_optionValue => $_optionLabel];
                })->toArray(),
            ];
        });
    }
}
