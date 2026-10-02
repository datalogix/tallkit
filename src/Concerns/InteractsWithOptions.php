<?php

namespace TALLKit\Concerns;

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;

trait InteractsWithOptions
{
    public function parseOptions(ComponentAttributeBag $attributes): array
    {
        $options = $attributes->pluck('options');
        $optionValue = $attributes->pluck('option-value');
        $optionLabel = $attributes->pluck('option-label');
        $optionGroupLabel = $attributes->pluck('option-group-label');
        $optionGroupChildren = $attributes->pluck('option-group-children');
        $optionGroupChildrenValue = $attributes->pluck('option-group-children-value');
        $optionGroupChildrenLabel = $attributes->pluck('option-group-children-label');

        if (is_subclass_of($options, \UnitEnum::class)) {
            return Arr::mapWithKeys($options::cases(), fn ($enum) => $this->enumOption($enum));
        }

        // Any other class name is text, never instantiated.
        if (is_string($options) && is_subclass_of($options, Model::class)) {
            $options = $options::query();
        }

        if (is_string($options)) {
            $options = $options === '' ? [] : [$options];
        }

        if ($options instanceof Model || $options instanceof QueryBuilder) {
            $options = $options->get();
        }

        // Unwrapped, not toArray(): models keep their accessors.
        if ($options instanceof Collection) {
            $options = $options->all();
        }

        $useValueAsKey = is_array($options)
            && array_is_list($options)
            && collect($options)->every(fn ($value) => is_scalar($value));

        return collect($options)->mapWithKeys(function ($value, $key) use ($useValueAsKey, $optionValue, $optionLabel, $optionGroupLabel, $optionGroupChildren, $optionGroupChildrenValue, $optionGroupChildrenLabel) {
            if ($value instanceof \UnitEnum) {
                return $this->enumOption($value);
            }

            $_optionValue = $this->optionField($value, $optionValue, ['id', 'value']) ?? ($useValueAsKey ? $value : $key);
            $_optionLabel = $this->optionField($value, $optionLabel, ['name', 'label', 'title', 'text']) ?? $this->optionFallbackLabel($value);

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
                    $_optionValue = $this->optionField($value, $optionGroupChildrenValue ?? $optionValue, ['id', 'value']) ?? $key;
                    $_optionLabel = $this->optionField($value, $optionGroupChildrenLabel ?? $optionLabel, ['name', 'label', 'title', 'text']) ?? $this->optionFallbackLabel($value);

                    return [$_optionValue => $_optionLabel];
                })->toArray(),
            ];
        })->all();
    }

    protected function optionField(mixed $option, ?string $path, array $defaults): mixed
    {
        if ($path !== null) {
            return data_get($option, $path);
        }

        foreach ($defaults as $default) {
            if (($found = data_get($option, $default)) !== null) {
                return $found;
            }
        }

        return null;
    }

    protected function optionFallbackLabel(mixed $option): mixed
    {
        return $option instanceof Model ? $option->getKey() : $option;
    }

    protected function enumOption(\UnitEnum $enum): array
    {
        return [
            ($enum instanceof \BackedEnum ? $enum->value : $enum->name) => method_exists($enum, 'label') ? $enum->label() : $enum->name,
        ];
    }
}
