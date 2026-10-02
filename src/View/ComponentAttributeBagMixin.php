<?php

namespace TALLKit\View;

use Illuminate\View\ComponentAttributeBag;
use TALLKit\Facades\TALLKit;

class ComponentAttributeBagMixin
{
    public function pluck()
    {
        return function (string $key, $default = null) {
            $result = $this->get($key);

            unset($this->attributes[$key]);

            return $result ?? ($default instanceof \Closure ? $default() : $default);
        };
    }

    public function classes()
    {
        return function (...$classes) {
            $this->offsetSet('class', ClassCache::merge((string) new ClassBuilder($classes), (string) $this->get('class', '')));

            return $this;
        };
    }

    /** merge() keeps null values, and a forwarded bag would override the other component's defaults. */
    public function mergeDefined()
    {
        return function (array|ComponentAttributeBag $defaults, bool $escape = true) {
            $defaults = $defaults instanceof ComponentAttributeBag ? $defaults->getAttributes() : $defaults;

            return $this->merge(array_filter($defaults, static fn (mixed $value): bool => $value !== null), $escape);
        };
    }

    public function wireKey()
    {
        return fn (mixed $key) => TALLKit::livewireRendering() && $key !== null ? $this->merge(['wire:key' => $key]) : $this;
    }

    public function prefixed()
    {
        return fn (string $prefix, string|bool $keepPrefix = false, array $with = []) => TALLKit::attributesPrefixed($this, $prefix, $keepPrefix, $with);
    }

    public function dataKey()
    {
        return fn (?string $key = null, mixed $value = true) => $this->when($key, fn ($attrs) => $attrs->merge([TALLKit::dataKey($key) => $value]));
    }
}
