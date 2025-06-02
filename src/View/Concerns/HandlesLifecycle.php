<?php

namespace TALLKit\View\Concerns;

use Illuminate\Support\Arr;

trait HandlesLifecycle
{
    private array $setVariables = [];

    private array $smartAttributes = [];

    protected function run(array $data)
    {
        $this->bootProps($data);
        $this->bootAttributes();

        foreach ($this->mount as $function) {
            $this->{$function}($data);
        }

        foreach ($this->setVariables as $attribute) {
            $data[$attribute] = $this->{$attribute};
        }

        foreach ($this->process as $function) {
            $this->{$function}($data);
        }

        $data['attributes'] = $this->attributes->except($this->smartAttributes);

        return tap($data, function (array &$data) {
            foreach ($this->finish as $function) {
                $this->{$function}($data);
            }
        });
    }

    protected function getData(string $attribute, mixed $default = null)
    {
        if ($this->attributes->has($kebab = str($attribute)->kebab()->toString())) {
            $this->smartAttributes($kebab);

            return $this->attributes->get($kebab);
        }

        if ($this->attributes->has($camel = str($attribute)->camel()->toString())) {
            $this->smartAttributes($camel);

            return $this->attributes->get($camel);
        }

        return $default;
    }

    protected function setVariables($variables)
    {
        collect(Arr::wrap($variables))->filter()->each(
            fn ($value) => $this->setVariables[] = $value,
        );
    }

    protected function smartAttributes($attributes)
    {
        collect(Arr::wrap($attributes))->filter()->each(
            fn ($value) => $this->smartAttributes[] = $value,
        );
    }
}
