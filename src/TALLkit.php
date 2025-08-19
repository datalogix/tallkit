<?php

namespace TALLKit;

use Illuminate\Support\Js;
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

    public function toast($text, $heading = null, $duration = null, $type = null, $position = null)
    {
        $data = compact('text', 'heading', 'duration', 'type', 'position');

        app('livewire')->current()->js('$tallkit.toast('.Js::encode($data).')');
    }
}
