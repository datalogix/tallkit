<?php

namespace TALLKit\Livewire\Mixins;

use TALLKit\Facades\TALLKit;

class ToastMixin
{
    public function toast()
    {
        return fn (...$args) => TALLKit::toast(...$args);
    }
}
