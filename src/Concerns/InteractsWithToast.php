<?php

namespace TALLKit\Concerns;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Session;
use TALLKit\TALLKit;

trait InteractsWithToast
{
    /** A string is escaped; only an HtmlString or a view is rendered as HTML. */
    public function toast(
        null|string|Htmlable $message = null,
        null|string|Htmlable $title = null,
        ?string $type = null,
        int|bool|null $duration = null,
        ?string $position = null,
        ?bool $progress = null,
        ?string $size = null,
        ?bool $invert = null,
        ?array $actions = null,
        ?string $id = null,
    ): ?object {
        if (func_num_args() === 0) {
            return $this->withTypeProxy('toast');
        }

        $html = $message instanceof Htmlable || $title instanceof Htmlable;
        $message = $this->toastContent($message, $html);
        $title = $this->toastContent($title, $html);
        $html = $html ?: null;

        $component = $this->livewireComponent();

        if (! $component) {
            $this->flashToast(array_filter(
                compact('message', 'title', 'type', 'duration', 'position', 'progress', 'size', 'invert', 'actions', 'id', 'html'),
                fn ($value) => $value !== null,
            ));

            return null;
        }

        if ($actions !== null) {
            $actions = array_map(
                fn (array $action) => isset($action['method']) ? [...$action, 'component' => $action['component'] ?? $component?->getId()] : $action,
                $actions,
            );
        }

        $component->js('$tallkit.toast', array_filter(
            compact('message', 'title', 'type', 'duration', 'position', 'progress', 'size', 'invert', 'actions', 'id', 'html'),
            fn ($value) => $value !== null,
        ));

        return null;
    }

    protected function toastContent(null|string|Htmlable $value, bool $html): ?string
    {
        return match (true) {
            $value instanceof Htmlable => $value->toHtml(),
            $html && $value !== null => e($value),
            default => $value,
        };
    }

    protected function toastSessionKey(): string
    {
        return $this->storageKey('toasts');
    }

    public function flashToast(array $toast): void
    {
        if (! $this->sessionStarted()) {
            return;
        }

        Session::flash($this->toastSessionKey(), [...Session::get($this->toastSessionKey(), []), $toast]);
    }

    public function toastPullFlashed(): array
    {
        if (! $this->sessionStarted()) {
            return [];
        }

        return (array) Session::pull($this->toastSessionKey(), []);
    }

    protected function toastClose(string $id)
    {
        return $this->livewireComponent()?->js('$tallkit.toast().close', $id);
    }

    /** Passed by name, so `type` never collides with the positional argument. */
    protected function withTypeProxy(string $target)
    {
        $names = collect((new \ReflectionMethod($this, $target))->getParameters())
            ->map(fn (\ReflectionParameter $parameter) => $parameter->getName())
            ->reject(fn (string $name) => $name === 'type')
            ->sortBy(fn (string $name) => match ($name) {
                'message' => 0,
                'title' => 1,
                default => 2,
            })
            ->values()
            ->all();

        return new class($this, $target, $names)
        {
            public function __construct(
                protected TALLKit $tallkit,
                protected string $target,
                protected array $names,
            ) {}

            public function __call(string $method, array $arguments)
            {
                if ($this->target === 'toast' && $method === 'close') {
                    return (fn () => $this->toastClose(...$arguments))->call($this->tallkit);
                }

                $named = ['type' => $method];

                foreach ($arguments as $key => $value) {
                    $named[is_int($key) ? ($this->names[$key] ?? $key) : $key] = $value;
                }

                return $this->tallkit->{$this->target}(...$named);
            }
        };
    }
}
