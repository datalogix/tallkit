<?php

namespace TALLKit;

use TALLKit\Assets\AssetManager;
use TALLKit\Binders\FormDataBinder;
use TALLKit\Components\Icon\Icon;
use TALLKit\View\ClassBuilder;

class TALLKit
{
    public function scripts(?array $options = null)
    {
        return AssetManager::scripts($options);
    }

    public function classes(...$classes)
    {
        return new ClassBuilder($classes);
    }

    public function bind($target)
    {
        return app(FormDataBinder::class)->bind($target);
    }

    public function endBind()
    {
        return app(FormDataBinder::class)->endBind();
    }

    public function setIconCollections(array $collections)
    {
        Icon::setCollections($collections);
    }

    public function toast(...$args)
    {
        app('livewire')->current()->toast(...$args);
    }
}
