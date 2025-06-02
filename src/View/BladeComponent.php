<?php

namespace TALLKit\View;

use AllowDynamicProperties;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

#[AllowDynamicProperties]
abstract class BladeComponent extends Component
{
    public static array $aliases = [];

    use Concerns\HandlesAssetInjection,
        Concerns\HandlesAttributes,
        Concerns\HandlesLifecycle,
        Concerns\HandlesProps,
        Concerns\HandlesView;

    public function classes(...$classes)
    {
        return new ClassBuilder($classes);
    }

    public function isSlot($slot)
    {
        return $slot instanceof ComponentSlot;
    }

    public function attributesAfter($prefix, array $default = [], string|bool $slot = true)
    {
        $attrs = new ComponentAttributeBag($default);

        foreach ($this->attributes->whereStartsWith($prefix)->getAttributes() as $key => $value) {
            $attrs[substr($key, strlen($prefix))] = $value;
        }

        if (! $slot) {
            return $attrs;
        }

        $prop = Str::of(is_string($slot) ? $slot : $prefix)
            ->replaceLast(':', '')
            ->camel()
            ->toString();

        if (! property_exists($this, $prop)) {
            return $attrs;
        }

        return $this->isSlot($this->{$prop})
            ? $attrs->merge($this->{$prop}->attributes->getAttributes())
            : $attrs;
    }
}
