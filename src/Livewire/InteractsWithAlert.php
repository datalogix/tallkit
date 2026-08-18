<?php

namespace TALLKit\Livewire;

use TALLKit\Facades\TALLKit;

trait InteractsWithAlert
{
    public function alert()
    {
        return fn (...$args) => TALLKit::alert(...$args);
    }

    public function alerts()
    {
        return fn () => TALLKit::alerts();
    }
}
