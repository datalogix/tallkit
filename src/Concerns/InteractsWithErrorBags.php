<?php

namespace TALLKit\Concerns;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ComponentSlot;

trait InteractsWithErrorBags
{
    public function errorBag(?string $bag = null): MessageBag
    {
        $errors = app('view')->shared('errors') ?? session('errors') ?? new ViewErrorBag;

        return $errors->getBag($this->errorBagName($bag) ?? 'default');
    }

    protected function errorBagName(?string $bag = null): ?string
    {
        return $bag ?: (app('view')->getConsumableComponentData('errorBag') ?: null);
    }

    protected function errorKey(string $name): string
    {
        $name = Str::replace('[]', '[*]', Str::beforeLast(Str::finish($name, '[]'), '[]'));

        return Str::replace(['[', ']'], ['.', ''], $name);
    }

    public function hasError(string $name, ?string $bag = null): bool
    {
        $key = $this->errorKey($name);
        $errorBag = $this->errorBag($bag);

        return $errorBag->has($key) || $errorBag->has($key.'.*');
    }

    public function errorMessage(
        ?string $name = null,
        ?ComponentSlot $slot = null,
        ?string $bag = null,
    ): string|ComponentSlot|null {
        $errorBag = $this->errorBag($bag);
        $key = $name ? $this->errorKey($name) : null;
        $message = $key ? $errorBag->first($key) : $slot;

        if ($key && (is_null($message) || $message === '')) {
            $message = $errorBag->first($key.'.*');
        }

        return $message;
    }
}
