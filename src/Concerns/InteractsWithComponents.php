<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Facades\Session;

trait InteractsWithComponents
{
    public function alert(
        null|string|array $message = null,
        ?string $type = null,
        string|bool|null $icon = null,
        string|bool|null $border = null,
        ?string $title = null,
        ?array $list = null,
        string|bool|null $dismissible = null,
        int|bool|null $timeout = null,
        ?string $size = null,
        ?string $name = null,
    ) {
        if (blank($message) && blank($title) && blank($list)) {
            return;
        }

        Session::flash($name ?? 'status', [
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'border' => $border,
            'title' => $title,
            'list' => $list,
            'dismissible' => $dismissible,
            'timeout' => $timeout,
            'size' => $size,
        ]);
    }

    public function alerts()
    {
        return new class($this)
        {
            public function __construct(
                protected TALLKit $tallkit,
            ) {}

            public function __call(string $method, array $arguments)
            {
                $arguments['type'] = $method;

                return $this->tallkit->alert(...$arguments);
            }
        };
    }

    public function modal(string $name, bool $scope = false)
    {
        return new class($name, $scope)
        {
            public function __construct(
                protected string $name,
                protected ?bool $scope
            ) {}

            public function show()
            {
                $component = app('livewire')->current();

                if (! $component) {
                    return;
                }

                $component->dispatch(
                    'modal-show',
                    name: $this->name,
                    scope: $this->scope ? $component->getId() : null
                );
            }

            public function close()
            {
                $component = app('livewire')->current();

                if (! $component) {
                    return;
                }

                $component->dispatch(
                    'modal-close',
                    name: $this->name,
                    scope: $this->scope ? $component->getId() : null
                );
            }
        };
    }

    public function modals()
    {
        return new class
        {
            public function close()
            {
                app('livewire')->current()?->dispatch('modal-close');
            }
        };
    }

    public function toast(
        ?string $message = null,
        ?string $title = null,
        ?string $type = null,
        ?int $duration = null,
        ?string $position = null,
        ?bool $progress = null,
        ?string $size = null,

    ) {
        return app('livewire')->current()?->js(
            '$tallkit.toast',
            $message,
            $title,
            $type,
            $duration,
            $position,
            $progress,
            $size,
        );
    }

    public function toasts()
    {
        return new class($this)
        {
            public function __construct(
                protected TALLKit $tallkit,
            ) {}

            public function __call(string $method, array $arguments)
            {
                $arguments['type'] = $method;

                return $this->tallkit->toast(...$arguments);
            }
        };
    }
}
