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

    public function modal()
    {
        return fn ($name) => TALLKit::modal($name, scope: true);
    }

    public function modals()
    {
        return fn () => TALLKit::modals();
    }
}
