<?php

namespace TALLKit\Concerns;

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
        return collect($this->options)->mapWithKeys(function ($value, $key) {
            $optionValue = data_get($value, $this->optionValue ?? 'id', $key);
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
