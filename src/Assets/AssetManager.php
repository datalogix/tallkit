<?php

namespace TALLKit\Assets;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use TALLKit\Http\Controllers\AssetController;

class AssetManager
{
    public $hasRenderedScripts = false;

    public $hasRenderedComponents = false;

    protected static ?string $versionHash = null;

    public static function boot()
    {
        $instance = new static;
        $instance->registerAssetDirective();
        $instance->registerAssetRoutes();

        app()->instance(static::class, $instance);

        $instance->listenForComponents();

        AssetInjector::boot();
    }

    public function listenForComponents()
    {
        $views = hash('xxh128', 'tallkit').'::*';
        $event = 'composing: '.$views;

        app('events')->forget($event);

        View::composer($views, function () use ($event) {
            $this->hasRenderedComponents = true;

            app('events')->forget($event);
        });
    }

    public function registerAssetDirective()
    {
        Blade::directive('tallkitScripts', function ($expression) {
            return <<<PHP
            {!! app('tallkit')->scripts($expression) !!}
            PHP;
        });
    }

    public function registerAssetRoutes()
    {
        Route::get('/tallkit/tallkit.js', [AssetController::class, 'script'])->name('tallkit.script');
    }

    public static function scripts(?array $options = null)
    {
        app(static::class)->hasRenderedScripts = true;

        $nonce = $options['nonce'] ?? Vite::cspNonce();
        $nonce = $nonce ? ' nonce="'.e($nonce).'"' : '';

        $loadAlpine = config('tallkit.load_alpine', true) ? '' : ' data-load-alpine="false"';

        $tooltip = array_filter((array) config('tallkit.tooltip', []), fn ($value) => $value !== null && $value !== '');
        $tooltip = $tooltip ? " data-tooltip='".e(json_encode($tooltip))."'" : '';

        return '<script src="'.route('tallkit.script', ['id' => static::versionHash()]).'" data-navigate-once'.$loadAlpine.$tooltip.$nonce.'></script>';
    }

    protected static function versionHash(): string
    {
        if (static::$versionHash !== null) {
            return static::$versionHash;
        }

        $manifest = json_decode((string) @file_get_contents(__DIR__.'/../../dist/manifest.json'), true);

        return static::$versionHash = (string) ($manifest['/tallkit.js'] ?? @filemtime(__DIR__.'/../../dist/tallkit.js') ?: '');
    }
}
