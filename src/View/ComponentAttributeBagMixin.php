<?php

namespace TALLKit\View;

use TALLKit\Facades\TALLKit;

class ComponentAttributeBagMixin
{
    public function pluck()
    {
        return function (string $key, $default = null) {
            $result = $this->get($key);

            unset($this->attributes[$key]);

            return $result ?? $default;
        };
    }

    public function classes(...$classes)
    {
        return fn (...$classes) => $this->twMerge(new ClassBuilder($classes));
    }

    public function dataKey()
    {
        return fn (?string $key = null) => $this->when($key, fn ($attrs, $value) => $attrs->merge([TALLKit::dataKey($value) => (bool) $value]));
    }
}
