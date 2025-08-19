<?php

namespace TALLKit\Assets;

use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Str;

class AssetInjector
{
    protected static bool $hasRenderedAComponentThisRequest = false;

    public static function markComponentAsRendered()
    {
        static::$hasRenderedAComponentThisRequest = true;
    }

    public static function boot()
    {
        app('events')->listen(RequestHandled::class, function ($handled) {
            if (Str::doesntContain($handled->response->headers->get('content-type'), 'text/html', true)) {
                return;
            }

            if (! method_exists($handled->response, 'status') || $handled->response->status() !== 200) {
                return;
            }

            $assetsHead = '';
            $assetsBody = '';

            if (static::shouldInjectAssets()) {
                $assetsHead .= AssetManager::styles()."\n";
                $assetsBody .= AssetManager::scripts()."\n";
            }

            if ($assetsHead === '' && $assetsBody === '') {
                return;
            }

            $html = $handled->response->getContent();

            if (Str::contains($html, '</html>', true)) {
                $originalContent = $handled->response->original;
                $handled->response->setContent(static::injectAssets($html, $assetsHead, $assetsBody));
                $handled->response->original = $originalContent;
            }
        });
    }

    protected static function shouldInjectAssets()
    {
        if (
            config('tallkit.inject_assets', true) === false
            || ! static::$hasRenderedAComponentThisRequest
            || app(AssetManager::class)->hasRenderedScripts
        ) {
            return false;
        }

        return true;
    }

    protected static function injectAssets(string $html, string $assetsHead, string $assetsBody)
    {
        $html = Str::of($html);

        if ($html->test('/<\s*\/\s*head\s*>/i') && $html->test('/<\s*\/\s*body\s*>/i')) {
            return $html
                ->replaceMatches('/(<\s*\/\s*head\s*>)/i', $assetsHead.'$1')
                ->replaceMatches('/(<\s*\/\s*body\s*>)/i', $assetsBody.'$1')
                ->toString();
        }

        return $html
            ->replaceMatches('/(<\s*html(?:\s[^>])*>)/i', '$1'.$assetsHead)
            ->replaceMatches('/(<\s*\/\s*html\s*>)/i', $assetsBody.'$1')
            ->toString();
    }
}
