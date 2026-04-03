<?php

namespace TALLKit;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use TALLKit\Assets\AssetManager;
use TALLKit\Concerns\InteractsWithAvatar;
use TALLKit\Concerns\InteractsWithComponents;
use TALLKit\Concerns\InteractsWithErrorBags;
use TALLKit\Concerns\InteractsWithIcon;
use TALLKit\Concerns\InteractsWithSize;
use TALLKit\Concerns\InteractsWithTable;
use TALLKit\Concerns\InteractsWithUser;
use TALLKit\View\ClassBuilder;

class TALLKit
{
    use InteractsWithAvatar;
    use InteractsWithComponents;
    use InteractsWithErrorBags;
    use InteractsWithIcon;
    use InteractsWithSize;
    use InteractsWithTable;
    use InteractsWithUser;

    public function dataKey(string $name)
    {
        return 'data-tallkit-'.$name;
    }

    public function scripts(?array $options = null)
    {
        return AssetManager::scripts($options);
    }

    public function classes(...$classes)
    {
        return new ClassBuilder($classes);
    }

    public function isSlot($slot)
    {
        return $slot instanceof ComponentSlot;
    }

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
}
