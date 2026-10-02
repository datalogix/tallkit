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
                // Shared by every request of an Octane worker.
                app(AssetManager::class)->hasRenderedScripts = false;
                app(AssetManager::class)->hasRenderedComponents = false;
            }
        });
    }

    protected static function handle(RequestHandled $handled)
    {
        if (Str::doesntContain((string) $handled->response->headers->get('content-type'), 'text/html', true)) {
            return;
        }

        if (! method_exists($handled->response, 'status') || $handled->response->status() !== 200) {
            return;
        }

        if (! static::shouldInjectAssets()) {
            return;
        }

        $assetsBody = AssetManager::scripts()."\n";

        $html = $handled->response->getContent();

        if (is_string($html) && Str::contains($html, '</html>', true)) {
            $originalContent = $handled->response->original;
            $handled->response->setContent(static::injectAssets($html, $assetsBody));
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
        foreach (['body', 'html'] as $tag) {
            if (preg_match_all('/<\s*\/\s*'.$tag.'\s*>/i', $html, $matches, PREG_OFFSET_CAPTURE)) {
                $offset = end($matches[0])[1];

                return substr_replace($html, $assetsBody, $offset, 0);
            }
        }

        return $html;
    }
}
