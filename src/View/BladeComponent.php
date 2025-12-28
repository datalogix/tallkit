<?php

namespace TALLKit\View;

use AllowDynamicProperties;
use Arr;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

#[AllowDynamicProperties]
abstract class BladeComponent extends Component
{
    use Concerns\HandlesAssetInjection,
        Concerns\HandlesAttributes,
        Concerns\HandlesDataKey,
        Concerns\HandlesLifecycle,
        Concerns\HandlesProps,
        Concerns\HandlesSize,
        Concerns\HandlesView;

    public function classes(...$classes)
    {
        return new ClassBuilder($classes);
    }

    public function isSlot($slot)
    {
        return $slot instanceof ComponentSlot;
    }

    public function after(...$args)
    {
        return $this->attributesAfter(...$args);
    }

    public function attributesAfter(
        $prefix,
        array|ComponentAttributeBag $default = [],
        string|bool $slot = true,
        string|bool $prepend = false,
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

        if ($dataKey = $this->dataKey($prop)) {
            $attrs = $attrs->merge([$dataKey => '']);
        }

        foreach ($this->attributes->whereStartsWith($prefix)->getAttributes() as $key => $value) {
            $attrs[substr($key, strlen($prefix))] = $value;
        }

        if ($slot && property_exists($this, $prop) && $this->isSlot($this->{$prop})) {
            $attrs = $attrs->merge($this->{$prop}->attributes->getAttributes());
        }

        if ($prepend) {
            $attrs = new ComponentAttributeBag(Arr::mapWithKeys(
                $attrs->whereDoesntStartWith($this->buildDataAttribute(''))->getAttributes(),
                fn ($value, $key) => [(is_string($prepend) ? $prepend : $prefix).$key => $value]
            ));
        }

        return $attrs;
    }
}
