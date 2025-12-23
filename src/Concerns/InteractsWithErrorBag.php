<?php

namespace TALLKit\Concerns;

use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ComponentSlot;
use TALLKit\Attributes\Mount;

trait InteractsWithErrorBag
{
    #[Mount(15)]
    protected function mountErrorBag(array $data)
    {
        $this->manageProp($data, 'bag');
    }

    public function getErrorBag()
    {
        $errors = app('view')->shared('errors') ?? session('errors') ?? new ViewErrorBag;

        return $errors->getBag($this->bag ?? 'default');
    }

    public function hasError(string $name)
    {
        return $this->getErrorBag()->has($name);
    }

    public function getError(
        ?string $name = null,
        ?ComponentSlot $slot = null,
    ) {
        $errorBag = $this->getErrorBag();
        $message = $name ? $errorBag->first($name) : $slot;

        if ($name && (is_null($message) || $message === '')) {
            $message = $errorBag->first($name.'.*');
        }

        return $message;
    }
}
