<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

if (! function_exists('is_livewire')) {
    function in_livewire()
    {
        return app('tallkit')->inLivewire();
    }
}

if (! function_exists('route_detect')) {
    function route_detect($routes, $parameters = null, $default = '/')
    {
        foreach (array_filter(Arr::wrap($routes)) as $route) {
            if (Route::has($route)) {
                return route($route, $parameters);
            }
        }

        return $default;
    }
}
