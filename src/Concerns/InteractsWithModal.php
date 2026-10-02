<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Facades\Session;
use TALLKit\TALLKit;

trait InteractsWithModal
{
    public function modal(string $name, bool $scope = false): object
    {
        return new class($this, $name, $scope)
        {
            public function __construct(
                protected TALLKit $tallkit,
                protected string $name,
                protected ?bool $scope
            ) {}

            public function show(): void
            {
                $component = $this->tallkit->livewireComponent();

                if (! $component) {
                    $this->tallkit->flashModal($this->name);

                    return;
                }

                $component->dispatch(
                    $this->tallkit->eventName('modal-show'),
                    name: $this->name,
                    scope: $this->scope ? $component->getId() : null
                );
            }

            public function close(): void
            {
                $component = $this->tallkit->livewireComponent();

                if (! $component) {
                    return;
                }

                $component->dispatch(
                    $this->tallkit->eventName('modal-close'),
                    name: $this->name,
                    scope: $this->scope ? $component->getId() : null
                );
            }
        };
    }

    public function modals(): object
    {
        return new class($this)
        {
            public function __construct(
                protected TALLKit $tallkit,
            ) {}

            public function close(): void
            {
                $component = $this->tallkit->livewireComponent();

                if (! $component) {
                    return;
                }

                $component->dispatch($this->tallkit->eventName('modal-close'));
            }
        };
    }

    protected function modalSessionKey(): string
    {
        return $this->storageKey('modals');
    }

    public function flashModal(string $name): void
    {
        if (! $this->sessionStarted()) {
            return;
        }

        Session::flash($this->modalSessionKey(), array_values(array_unique([...Session::get($this->modalSessionKey(), []), $name])));
    }

    public function modalFlashed(?string $name): bool
    {
        if ($name === null || ! $this->sessionStarted()) {
            return false;
        }

        return in_array($name, (array) Session::get($this->modalSessionKey(), []), true);
    }
}
