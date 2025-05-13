<?php

namespace TALLKit\Assets;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

class AssetsManager
{
    use CanPretendToBeAFile;

    public $hasRenderedScripts = false;

    public static function boot()
    {
        $instance = new static;
        $instance->registerAssetDirective();
        $instance->registerAssetRoutes();

        app()->instance(static::class, $instance);

        InjectAssets::boot();
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
        Route::get('/tallkit/tallkit.js', fn () => $this->pretendResponseIsFile(
            config('app.debug')
                ? __DIR__.'/../../dist/tallkit.js'
                : __DIR__.'/../../dist/tallkit.min.js'
        ))->name('tallkit-script');
    }

    public static function scripts($options = [])
    {
        app(static::class)->hasRenderedScripts = true;

        // $manifest = json_decode(file_get_contents(__DIR__ . '/../../dist/manifest.json'), true);
        // $versionHash = $manifest['/tallkit.js'];
        $versionHash = rand(1, 1000);
        $nonce = isset($options) && isset($options['nonce']) ? ' nonce="'.$options['nonce'].'"' : '';

        return '<script src="'.route('tallkit-script', ['id' => $versionHash]).'" data-navigate-once'.$nonce.'></script>';
    }

    public static function styles($options = [])
    {
        return '';
    }
}
