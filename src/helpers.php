<?php

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use TALLKit\Facades\TALLKit;

if (! function_exists('route_detect')) {
    function route_detect(array|string|null $routes, $parameters = null, ?string $default = '/')
    {
        foreach (array_filter(Arr::wrap($routes)) as $route) {
            if (Route::has($route)) {
                return route($route, $parameters);
            }
        }

        return $default;
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
            //
        }

        return null;
    }
}

if (! function_exists('find_asset')) {
    function find_asset(array|string $paths)
    {
        foreach (array_filter(Arr::wrap($paths)) as $path) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return null;
    }
}

if (! function_exists('find_image')) {
    function find_image(string $name, array $dirs = ['', 'imgs/', 'images/'], array $exts = ['png', 'jpg', 'jpeg'])
    {
        $paths = collect($dirs)->flatMap(fn ($dir) => collect($exts)->map(fn ($ext) => "{$dir}{$name}.{$ext}"))->all();

        return find_asset($paths);
    }
}

if (! function_exists('in_livewire')) {
    function in_livewire()
    {
        return \Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent();
    }
}

if (! function_exists('is_current_href')) {
    function is_current_href(?string $href = null, ?bool $exact = null)
    {
        $hrefForCurrentDetection = Str::startsWith($href, trim(config('app.url')))
            ? Str::after($href, trim(config('app.url'), '/'))
            : $href;

        if ($hrefForCurrentDetection === '') {
            $hrefForCurrentDetection = '/';
        }

        if ($hrefForCurrentDetection !== '/') {
            $hrefForCurrentDetection = trim($hrefForCurrentDetection, '/');
        }

        if (! $hrefForCurrentDetection) {
            return false;
        }

        return app('livewire')?->isLivewireRequest()
            ? Str::is($hrefForCurrentDetection, app('livewire')->originalPath())
            : request()->is($exact ? $hrefForCurrentDetection : [$hrefForCurrentDetection, "$hrefForCurrentDetection/*"]);
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
