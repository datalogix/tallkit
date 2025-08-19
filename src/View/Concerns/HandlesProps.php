<?php

namespace TALLKit\View\Concerns;

use Illuminate\Support\Str;

trait HandlesProps
{
    protected array $props = [];

    protected function props()
    {
        return $this->props;
    }

    protected function bootProps(array $data)
    {
        $props = static::extractConstructorParameters();

        foreach ($props as $prop) {
            $this->manageProp($data, $prop);
        }

        foreach ($this->props() as $prop => $value) {
            $this->manageProp(
                $data,
                is_numeric($prop) ? $value : $prop,
                is_numeric($prop) ? null : $value,
            );
        }
    }

    protected function manageProp(array $data, string $prop, mixed $value = null)
    {
        $field = Str::camel($prop);
        $this->{$field} = $this->getData($field, data_get($data, $prop, $value));
        $this->setVariables($field);
    }
}
