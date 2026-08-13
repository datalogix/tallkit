<?php

namespace TALLKit;

use Illuminate\Support\Str;
use Illuminate\View\ComponentSlot;
use TALLKit\Assets\AssetManager;
use TALLKit\Concerns\InteractsWithAttributes;
use TALLKit\Concerns\InteractsWithAvatar;
use TALLKit\Concerns\InteractsWithComponents;
use TALLKit\Concerns\InteractsWithElement;
use TALLKit\Concerns\InteractsWithErrorBags;
use TALLKit\Concerns\InteractsWithField;
use TALLKit\Concerns\InteractsWithIcon;
use TALLKit\Concerns\InteractsWithOptions;
use TALLKit\Concerns\InteractsWithSize;
use TALLKit\Concerns\InteractsWithTable;
use TALLKit\Concerns\InteractsWithUpload;
use TALLKit\Concerns\InteractsWithUser;
use TALLKit\View\ClassBuilder;

class TALLKit
{
    use InteractsWithAttributes;
    use InteractsWithAvatar;
    use InteractsWithComponents;
    use InteractsWithElement;
    use InteractsWithErrorBags;
    use InteractsWithField;
    use InteractsWithIcon;
    use InteractsWithOptions;
    use InteractsWithSize;
    use InteractsWithTable;
    use InteractsWithUpload;
    use InteractsWithUser;

    public function dataKey(string $name)
    {
        return 'data-tallkit-'.$name;
    }

    public function generateId(?string $prefix = null, ?string $name = null, ?string $suffix = null)
    {
        return Str::slug(collect(['tallkit', $prefix, $name ?? Str::random(8), $suffix])
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->implode('-'));
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
}
