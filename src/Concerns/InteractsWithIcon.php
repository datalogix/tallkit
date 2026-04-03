<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

trait InteractsWithIcon
{
    public function getOrFetchSvgIcon($name)
    {
        if (! $name) {
            return null;
        }

        return Cache::driver('file')->rememberForever("tallkit-icon-{$name}", function () use ($name) {
            $path = storage_path('app/tallkit/icons/'.Str::snake($name).'.svg');

            if (File::exists($path)) {
                return File::get($path);
            }

            $response = Http::get("https://api.iconify.design/{$name}.svg");

            if (! $response->successful()) {
                return '';
            }

            $contents = $response->body();

            if (Str::doesntContain($contents, '<svg', true)) {
                return '';
            }

            File::ensureDirectoryExists(dirname($path));
            File::put($path, $contents);

            return $contents;
        });
    }
}
