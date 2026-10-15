<?php

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
use TALLKit\Facades\TALLKit;

if (! function_exists('route_detect')) {
    function route_detect(array|string|null $routes, $parameters = null, ?string $default = '/')
    {
        return TALLKit::routeDetect($routes, $parameters, $default);
    }
}

if (! function_exists('has_content')) {
    function has_content(mixed ...$values): bool
    {
        foreach ($values as $value) {
            if (TALLKit::hasContent($value)) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('string_value')) {
    function string_value(mixed $value, mixed $default = null): mixed
    {
        return is_string($value) && $value !== '' ? $value : $default;
    }
}

if (! function_exists('make_model')) {
    function make_model(string $class)
    {
        if (class_exists($class)) {
            return app($class);
        }

        try {
            return app(Str::of($class)->studly()->prepend('\App\Models\\')->toString());
        } catch (BindingResolutionException $e) {

        }

        return null;
    }
}

if (! function_exists('in_livewire')) {
    function in_livewire()
    {
        return TALLKit::livewireRendering();
    }
}

if (! function_exists('unless_livewire')) {
    function unless_livewire(mixed $value): mixed
    {
        return in_livewire() ? null : $value;
    }
}

if (! function_exists('toast')) {
    function toast(...$args)
    {
        return TALLKit::toast(...$args);
    }
}

if (! function_exists('alert')) {
    function alert(...$args)
    {
        return TALLKit::alert(...$args);
    }
}
