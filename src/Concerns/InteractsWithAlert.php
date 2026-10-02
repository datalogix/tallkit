<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Facades\Session;

trait InteractsWithAlert
{
    public function alert(
        null|string|array $message = null,
        ?string $type = null,
        string|bool|null $icon = null,
        string|bool|null $border = null,
        ?string $title = null,
        string|bool|null $closable = null,
        int|bool|null $duration = null,
        ?string $size = null,
        ?string $name = null,
        string|bool|null $progress = null,
        ?bool $pauseOnHover = null,
    ): ?object {
        if (func_num_args() === 0) {
            return $this->withTypeProxy('alert');
        }

        if ((blank($message) && blank($title)) || ! $this->sessionStarted()) {
            return null;
        }

        $key = $name ?? 'status';

        $alert = [
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'border' => $border,
            'title' => $title,
            'closable' => $closable,
            'duration' => $duration,
            'size' => $size,
            'progress' => $progress,
            'pauseOnHover' => $pauseOnHover,
        ];

        $previous = in_array($key, (array) Session::get('_flash.new', []), true) ? Session::get($key) : null;

        Session::flash($key, match (true) {
            $previous === null => $alert,
            is_array($previous) && array_is_list($previous) => [...$previous, $alert],
            default => [$previous, $alert],
        });

        return null;
    }
}
