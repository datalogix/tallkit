<?php

namespace TALLKit\Assets;

use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Str;

class AssetInjector
{
    public static function boot()
    {
        app('events')->listen(RequestHandled::class, function ($handled) {
            try {
                static::handle($handled);
            } finally {
                app(AssetManager::class)->hasRenderedScripts = false;
                app(AssetManager::class)->hasRenderedComponents = false;
                app(AssetManager::class)->listenForComponents();
            }
        });
    }

    protected static function handle(RequestHandled $handled)
    {
        if (Str::doesntContain((string) $handled->response->headers->get('content-type'), 'text/html', true)) {
            return;
        }

        if ($handled->response->isRedirection() || (config('app.debug') && $handled->response->isServerError())) {
            return;
        }

        if (! static::shouldInjectAssets()) {
            return;
        }

        $assetsBody = AssetManager::scripts()."\n";

        $html = $handled->response->getContent();

        if (is_string($html) && ($injected = static::injectAssets($html, $assetsBody)) !== null) {
            $originalContent = $handled->response->original;
            $handled->response->setContent($injected);
            $handled->response->original = $originalContent;
        }
    }

    protected static function shouldInjectAssets()
    {
        $inject = config('tallkit.inject_assets', 'auto');
        $manager = app(AssetManager::class);

        if ($inject === false || $manager->hasRenderedScripts) {
            return false;
        }

        return $inject === true || $manager->hasRenderedComponents
            || app('tallkit')->livewireComponentRendered();
    }

    // Before the last </body> (else </html>): an earlier one is in the content, like a string in an inline script.
    protected static function injectAssets(string $html, string $assetsBody)
    {
        $offset = strripos($html, '</html');

        if ($offset === false) {
            return null;
        }

        $offset = strripos($html, '</body', $offset - strlen($html)) ?: $offset;

        return substr_replace($html, $assetsBody, $offset, 0);
    }
}
