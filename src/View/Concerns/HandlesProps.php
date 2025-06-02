<?php

namespace TALLKit\View\Concerns;

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
            $this->manageProp($data, $prop, $value);
        }
    }

    private function manageProp(array $data, string $prop, mixed $value = null)
    {
        $field = str($prop)->camel()->toString();
        $this->{$field} = $this->getData($field, data_get($data, $prop, $value));
        $this->setVariables($field);
    }
}
