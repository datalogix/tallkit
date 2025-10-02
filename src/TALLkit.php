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
        return app('livewire')->current()?->js('$tk.toast', $text, $heading, $type, $duration, $position);
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

        return Session::flash($name ?? 'status', [
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
}
