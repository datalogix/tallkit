<?php

namespace TALLKit\Livewire;

use TALLKit\Facades\TALLKit;

trait InteractsWithModal
{
    public function modal()
    {
        return fn ($name) => TALLKit::modal($name, scope: true);
    }

    public function modals()
    {
        return fn () => TALLKit::modals();
    }
}
