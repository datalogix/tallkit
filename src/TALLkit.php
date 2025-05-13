<?php

namespace TALLKit;

use TALLKit\Assets\AssetsManager;
use TALLKit\Binders\FormDataBinder;
use TALLKit\View\ClassBuilder;

class TALLKit
{
    public function scripts($options = [])
    {
        return AssetsManager::scripts($options);
    }

    public function classes(...$classes)
    {
        return new ClassBuilder($classes);
    }

    public function inLivewire()
    {
        return \Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent();
    }

    public function bind($target)
    {
        return app(FormDataBinder::class)->bind($target);
    }

    public function endBind()
    {
        return app(FormDataBinder::class)->endBind();
    }
}
