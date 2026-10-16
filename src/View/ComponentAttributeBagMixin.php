<?php

namespace TALLKit\View;

use Illuminate\Support\Str;
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

    public function enabled()
    {
        return fn (string $key): bool => TALLKit::isAttributeEnabled($this->get($key));
    }

    public function withProps()
    {
        return function (array $scope, array ...$sets) {
            $props = [];
            $except = [];

            foreach ($sets as $set) {
                foreach ($set as $name => $default) {
                    if (isset($props[$name])) {
                        continue;
                    }

                    $key = Str::kebab($name);
                    $value = $scope[$name] ?? $this->get($key);

                    if ($value !== null) {
                        $props[$name] = $value;
                        $except[] = $key;
                    }
                }
            }

            return $this->except($except)->merge($props, escape: false);
        };
    }

    public function withElementProps()
    {
        return fn (array $scope, array ...$sets) => $this->withProps($scope, TALLKit::elementProps(), ...$sets);
    }

    public function withFieldProps()
    {
        return fn (array $scope, array ...$sets) => $this->withProps($scope, TALLKit::fieldProps(), ...$sets);
    }

    public function withFieldControlProps()
    {
        return fn (array $scope, array ...$sets) => $this->withProps($scope, TALLKit::fieldControlProps(), ...$sets);
    }

    public function withoutFieldPrefixes()
    {
        return fn (array $extra = []) => $this->whereDoesntStartWith([
            'field:', 'label:', 'info:', 'badge:', 'description:',
            'group:', 'prefix:', 'suffix:',
            'help:', 'error:',
            'control:',
            'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:', 'tooltip:',
            ...$extra,
        ]);
    }

    public function fieldContext()
    {
        return fn (null|bool|string $label = null, ?string $id = null, array $scope = []) => TALLKit::fieldContext($this, $label, $id, $scope);
    }

    public function wireKey()
    {
        return fn (mixed $key) => TALLKit::livewireRendering() && $key !== null ? $this->merge(['wire:key' => $key]) : $this;
    }

    public function prefixed()
    {
        return function (string $prefix, string|bool $keepPrefix = false, array $with = []) {
            $values = [];

            foreach ($this->whereStartsWith($prefix)->getAttributes() as $key => $value) {
                $key = substr($key, \strlen($prefix));
                $values[$keepPrefix === false ? $key : (\is_string($keepPrefix) ? $keepPrefix : $prefix).$key] = $value;
            }

            $prefixed = new ComponentAttributeBag($values);

            foreach ($with as $as => $withPrefix) {
                $prefixed = $prefixed->merge(
                    $this->prefixed($withPrefix, keepPrefix: \is_string($as) ? $as : true)->getAttributes(),
                    escape: false,
                );
            }

            return $prefixed;
        };
    }

    public function dataKey()
    {
        return fn (?string $key = null, mixed $value = true) => $this->when($key, fn ($attrs) => $attrs->merge([TALLKit::dataKey($key) => $value]));
    }
}
