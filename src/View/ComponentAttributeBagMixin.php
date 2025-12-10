<?php

namespace TALLKit\View;

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
}
