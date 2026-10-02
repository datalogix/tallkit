<?php

namespace TALLKit\Livewire\Mixins;

use TALLKit\Facades\TALLKit;

class ModalMixin
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
