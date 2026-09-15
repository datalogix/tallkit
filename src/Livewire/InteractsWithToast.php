<?php

namespace TALLKit\Livewire;

use TALLKit\Facades\TALLKit;

trait InteractsWithToast
{
    public function toast()
    {
        return fn (...$args) => TALLKit::toast(...$args);
    }

    public function toasts()
    {
        return fn () => TALLKit::toasts();
    }

    public function closeToast()
    {
        return fn (...$args) => TALLKit::closeToast(...$args);
    }
}
