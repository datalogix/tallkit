<?php

namespace TALLKit\Livewire;

use TALLKit\Facades\TALLKit;

class ComponentMixin
{
    public function toast()
    {
        return fn (...$args) => TALLKit::toast(...$args);
    }

    public function alert()
    {
        return fn (...$args) => TALLKit::alert(...$args);
    }
}
