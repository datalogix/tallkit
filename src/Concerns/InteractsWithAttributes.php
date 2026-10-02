<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

trait InteractsWithAttributes
{
    // disabled="false" or "0" written in Blade arrives as a string: off, as false and null are.
    public function isAttributeEnabled(mixed $value): bool
    {
        return ! in_array($value, [null, false, '', '0', 0, 'false'], true);
    }

    public function attributesMerge(array|ComponentAttributeBag|null ...$sets): ComponentAttributeBag
    {
        $attributes = null;

        foreach ($sets as $set) {
            if ($set === null) {
                continue;
            }

            $values = array_filter(
                $set instanceof ComponentAttributeBag ? $set->getAttributes() : $set,
                static fn (mixed $value): bool => $value !== null,
            );

            // merge() would rewrite a lone "style".
            $attributes = $attributes === null
                ? new ComponentAttributeBag($values)
                : (new ComponentAttributeBag($values))->merge($attributes->getAttributes(), escape: false);
        }

        return $attributes ?? new ComponentAttributeBag;
    }

    public function attributesPrefixed(
        ComponentAttributeBag $attributes,
        string $prefix,
        string|bool $keepPrefix = false,
        array $with = [],
    ): ComponentAttributeBag {
        $values = [];

        foreach ($attributes->whereStartsWith($prefix)->getAttributes() as $key => $value) {
            $key = substr($key, \strlen($prefix));
            $values[$keepPrefix === false ? $key : (\is_string($keepPrefix) ? $keepPrefix : $prefix).$key] = $value;
        }

        $prefixed = new ComponentAttributeBag($values);

        foreach ($with as $as => $withPrefix) {
            // Not escaped again: they come from the bag, escaped already.
            $prefixed = $prefixed->merge(
                $this->attributesPrefixed($attributes, $withPrefix, keepPrefix: \is_string($as) ? $as : true)->getAttributes(),
                escape: false,
            );
        }

        return $prefixed;
    }

    public function attributesWithProps(
        ComponentAttributeBag $attributes,
        array $scope,
        array ...$sets
    ): ComponentAttributeBag {
        static $kebab = [];

        $props = [];
        $except = [];

        foreach ($sets as $set) {
            foreach ($set as $name => $default) {
                if (isset($props[$name])) {
                    continue;
                }

                $key = $kebab[$name] ??= Str::kebab($name);
                $value = $scope[$name] ?? $attributes->get($key);

                if ($value !== null) {
                    $props[$name] = $value;
                    $except[] = $key;
                }
            }
        }

        return $attributes->except($except)->merge($props, escape: false);
    }

    public function attributesFromItem(mixed $item, string $key = 'label'): array
    {
        if ($item instanceof ComponentAttributeBag) {
            return $item->getAttributes();
        }

        return array_map(
            static fn (mixed $value): mixed => \is_string($value) ? e($value) : $value,
            \is_array($item) ? $item : [$key => $item],
        );
    }
}
