<?php

namespace TALLKit\Concerns;

use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ComponentSlot;

trait InteractsWithErrorBags
{
    public function getErrorBag(?string $bag = null)
    {
        $errors = app('view')->shared('errors') ?? session('errors') ?? new ViewErrorBag;

        return $errors->getBag($bag ?? 'default');
    }

    public function hasError(string $name, ?string $bag = null)
    {
        return $this->getErrorBag($bag)->has($name);
    }

    public function getError(
        ?string $name = null,
        ?ComponentSlot $slot = null,
        ?string $bag = null,
    ) {
        $errorBag = $this->getErrorBag($bag);
        $message = $name ? $errorBag->first($name) : $slot;

        if ($name && (is_null($message) || $message === '')) {
            $message = $errorBag->first($name.'.*');
        }

        return $message;
    }
}
