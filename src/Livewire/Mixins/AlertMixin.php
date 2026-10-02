<?php

namespace TALLKit\Livewire\Mixins;

use TALLKit\Facades\TALLKit;

class AlertMixin
{
    public function alert()
    {
        return fn (...$args) => TALLKit::alert(...$args);
    }
}
