<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Arr;
use TALLKit\Attributes\Mount;
use TALLKit\Attributes\Process;

trait AppendsCustomAttributes
{
    protected function customAppendedAttributes()
    {
        return [];
    }

    protected function mergeCustomAppendedAttributes()
    {
        return $this->customAppendedAttributes();
    }

    #[Mount(10)]
    protected function mountCustomAttributes(array $data)
    {
        $customAppendedAttributes = $this->customAppendedAttributes();
        $mergeCustomAppendedAttributes = Arr::map(
            $this->mergeCustomAppendedAttributes(),
            fn ($value, $name) => is_numeric($name) ? $value : $name
        );
        $keys = [];

        foreach ($customAppendedAttributes as $name => $value) {
            $key = is_numeric($name) ? $value : $name;

            if (in_array($key, $mergeCustomAppendedAttributes)) {
                $keys[] = $key;
            }

            $this->manageProp($data, $key, is_numeric($name) ? null : $value);
        }

        $this->smartAttributes = array_diff($this->smartAttributes, $keys);
    }

    #[Process(10)]
    protected function processCustomAttributes(array $data)
    {
        $attributes = Arr::mapWithKeys($this->mergeCustomAppendedAttributes(), function ($value, $name) {
            $key = is_numeric($name) ? $value : $name;

            return [$key => $this->{$key}];
        });

        $this->attributes = $this->attributes->merge($attributes, false);

    }
}
