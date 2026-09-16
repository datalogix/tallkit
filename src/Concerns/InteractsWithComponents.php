<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Facades\Session;
use TALLKit\TALLKit;

trait InteractsWithComponents
{
    public function alert(
        null|string|array $message = null,
        ?string $type = null,
        string|bool|null $icon = null,
        string|bool|null $border = null,
        ?string $title = null,
        string|bool|null $dismissible = null,
        int|bool|null $timeout = null,
        ?string $size = null,
        ?string $name = null,
    ) {
        if (blank($message) && blank($title)) {
            return;
        }

        Session::flash($name ?? 'status', [
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'border' => $border,
            'title' => $title,
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
                $component = app('livewire')?->current();

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
                $component = app('livewire')?->current();

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
                $component = app('livewire')?->current();

                if (! $component) {
                    return;
                }

                $component->dispatch('modal-close');
            }
        };
    }

    public function toast(
        ?string $message = null,
        ?string $title = null,
        ?string $type = null,
        int|bool|null $duration = null,
        ?string $position = null,
        ?bool $progress = null,
        ?string $size = null,
        ?bool $invert = null,
        ?array $actions = null,
        ?string $id = null,
    ) {
        $component = app('livewire')?->current();

        if (! $component) {
            return;
        }

        if ($actions !== null) {
            $actions = array_map(
                fn (array $action) => isset($action['method']) ? [...$action, 'component' => $action['component'] ?? $component?->getId()] : $action,
                $actions,
            );
        }

        return $component?->js(
            '$tallkit.toast',
            $message,
            $title,
            $type,
            $duration,
            $position,
            $progress,
            $size,
            $invert,
            $actions,
            $id,
        );
    }

    public function closeToast(string $id)
    {
        return app('livewire')?->current()?->js('$tallkit.closeToast', $id);
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
