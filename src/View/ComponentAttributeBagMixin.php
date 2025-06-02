<?php

namespace TALLKit\View;

class ComponentAttributeBagMixin
{
    public function pluck()
    {
        return function (string $key) {
            $result = $this->get($key);

            unset($this->attributes[$key]);

            return $result;
        };
    }

    public function classes(...$classes)
    {
        return function (...$classes) {
            // todo: apply twClass (merge tailwind class)
            return $this->class(new ClassBuilder($classes));
        };
    }
}
