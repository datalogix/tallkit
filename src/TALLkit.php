<?php

namespace TALLKit;

use Illuminate\Support\Facades\Session;
use TALLKit\Assets\AssetManager;
use TALLKit\Binders\FormDataBinder;
use TALLKit\Components\Icon\Icon;
use TALLKit\View\ClassBuilder;

class TALLKit
{
    public function scripts(?array $options = null)
    {
        return AssetManager::scripts($options);
    }

    public function classes(...$classes)
    {
        return new ClassBuilder($classes);
    }

    public function bind($target)
    {
        return app(FormDataBinder::class)->bind($target);
    }

    public function endBind()
    {
        return app(FormDataBinder::class)->endBind();
    }

    public function setIconCollections(array $collections)
    {
        return Icon::setCollections($collections);
    }

    public function toast(
        ?string $text = null,
        ?string $heading = null,
        ?string $type = null,
        ?int $duration = null,
        ?string $position = null,
    ) {
        return app('livewire')->current()?->js('$tallkit.toast', $text, $heading, $type, $duration, $position);
    }

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

                $component->dispatch('modal-show', name: $this->name, scope: $this->scope ? $component->getId() : null);
            }

            public function close()
            {
                $component = app('livewire')->current();

                if (! $component) {
                    return;
                }

                $component->dispatch('modal-close', name: $this->name, scope: $this->scope ? $component->getId() : null);
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
}
