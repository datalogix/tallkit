<?php

namespace TALLKit\Livewire;

use Livewire\Component;
use TALLKit\Livewire\Mixins\AlertMixin;
use TALLKit\Livewire\Mixins\ModalMixin;
use TALLKit\Livewire\Mixins\NotificationMixin;
use TALLKit\Livewire\Mixins\ToastMixin;
use TALLKit\Livewire\Mixins\UploadMixin;

class ComponentMixins
{
    // Macros, not methods: Livewire never calls them from the browser.
    protected const MIXINS = [
        AlertMixin::class,
        ModalMixin::class,
        NotificationMixin::class,
        ToastMixin::class,
        UploadMixin::class,
    ];

    public static function register()
    {
        foreach (static::MIXINS as $mixin) {
            Component::mixin(new $mixin);
        }
    }
}
