<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

trait InteractsWithAttributes
{
    public function attributesAfter(
        ComponentAttributeBag $attributes,
        $prefix,
        array|ComponentAttributeBag $default = [],
        string|bool $slot = true,
        string|bool|array $prepend = false,
    ) {
        $attrs = new ComponentAttributeBag(
            $default instanceof ComponentAttributeBag
                ? $default->toArray()
                : $default
        );

        $prop = Str::of(is_string($slot) ? $slot : $prefix)
            ->replaceLast(':', '')
            ->camel()
            ->toString();

        foreach ($attributes->whereStartsWith($prefix)->getAttributes() as $key => $value) {
            $attrs[substr($key, strlen($prefix))] = $value;
        }

        if ($slot && property_exists($this, $prop) && $this->isSlot($this->{$prop})) {
            $attrs = $attrs->merge($this->{$prop}->attributes->getAttributes());
        }

        if (is_array($prepend)) {
            foreach ($prepend as $prependKey => $prependName) {
                $attrs = $attrs->merge(
                    $this->attributesAfter(
                        $attributes,
                        $prependName,
                        prepend: is_string($prependKey) ? $prependKey : true
                    )->getAttributes()
                );
            }
        } elseif ($prepend) {
            $attrs = new ComponentAttributeBag(Arr::mapWithKeys(
                $attrs->getAttributes(),
                fn ($value, $key) => [(is_string($prepend) ? $prepend : $prefix).$key => $value]
            ));
        }

        return $attrs;
    }

    public function mergeDefinedProps(
        ComponentAttributeBag $attributes,
        array $scope,
        array ...$propSets
    ): ComponentAttributeBag {
        $forwardProps = [];

        $propNames = [];

        foreach ($propSets as $propSet) {
            foreach (array_keys($propSet) as $propName) {
                $propNames[$propName] = true;
            }
        }

        foreach (array_keys($propNames) as $propName) {
            if (array_key_exists($propName, $scope) && $scope[$propName] !== null) {
                $forwardProps[$propName] = $scope[$propName];
            }
        }

        return $attributes->merge($forwardProps);
    }
}
