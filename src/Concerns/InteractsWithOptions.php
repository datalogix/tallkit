<?php

namespace TALLKit\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use TALLKit\Attributes\Mount;

trait InteractsWithOptions
{
    #[Mount(40)]
    protected function mountOptions(array $data)
    {
        $this->manageProp($data, 'options');
        $this->manageProp($data, 'optionValue');
        $this->manageProp($data, 'optionLabel');
        $this->manageProp($data, 'optionGroupLabel');
        $this->manageProp($data, 'optionGroupChildren');
        $this->manageProp($data, 'optionGroupChildrenValue');
        $this->manageProp($data, 'optionGroupChildrenLabel');

        $this->options = $this->parseOptions();
    }

    protected function parseOptions()
    {
        $options = $this->options;

        if (is_string($options) && class_exists($options)) {
            $options = app($options);
        }

        if ($options instanceof Model || $options instanceof Builder) {
            $options = $options->get();
        }

        $useValueAsKey = is_array($options) && array_keys($options) === range(0, count($options) - 1);

        return collect($options)->mapWithKeys(function ($value, $key) use ($useValueAsKey) {
            $optionValue = data_get($value, $this->optionValue ?? 'id') ?? ($useValueAsKey ? $value : $key);
            $optionLabel = data_get($value, $this->optionLabel ?? 'name', $value);

            if (! $this->optionGroupChildren && ! is_array($optionLabel)) {
                return [$optionValue => $optionLabel];
            }

            $optionGroupLabel = data_get($value, $this->optionGroupLabel ?? $this->optionLabel ?? 'name', $key);
            $optionGroupChildren = collect(data_get($value, $this->optionGroupChildren));

            if ($optionGroupChildren->isEmpty()) {
                return [];
            }

            return [
                $optionGroupLabel => $optionGroupChildren->mapWithKeys(function ($value, $key) {
                    $optionValue = data_get($value, $this->optionGroupChildrenValue ?? $this->optionValue ?? 'id', $key);
                    $optionLabel = data_get($value, $this->optionGroupChildrenLabel ?? $this->optionLabel ?? 'name', $value);

                    return [$optionValue => $optionLabel];
                })->toArray(),
            ];
        });
    }
}
