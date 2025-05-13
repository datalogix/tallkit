<?php

namespace TALLKit\Assets;

use Illuminate\Foundation\Http\Events\RequestHandled;

class InjectAssets
{
    public static $hasRenderedAComponentThisRequest = false;

    public static function boot()
    {
        app('events')->listen(RequestHandled::class, function ($handled) {
            if (! str($handled->response->headers->get('content-type'))->contains('text/html')) {
                return;
            }

            if (! method_exists($handled->response, 'status') || $handled->response->status() !== 200) {
                return;
            }

            $assetsHead = '';
            $assetsBody = '';

            if (static::shouldInjectAssets()) {
                $assetsHead .= AssetsManager::styles()."\n";
                $assetsBody .= AssetsManager::scripts()."\n";
            }

            if ($assetsHead === '' && $assetsBody === '') {
                return;
            }

            $html = $handled->response->getContent();

            if (str($html)->contains('</html>')) {
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
            // || ! static::$hasRenderedAComponentThisRequest
            || app(AssetsManager::class)->hasRenderedScripts
        ) {
            return false;
        }

        return true;
    }

    protected static function injectAssets($html, $assetsHead, $assetsBody)
    {
        $html = str($html);

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
