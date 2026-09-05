<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

trait InteractsWithIcon
{
    public function iconCacheKey(string $name)
    {
        return "tallkit-icon-{$name}";
    }

    public function iconStoragePath(string $name)
    {
        return storage_path('app/tallkit/icons/'.Str::snake($name).'.svg');
    }

    public function getOrFetchSvgIcon(?string $name)
    {
        if (! $name) {
            return null;
        }

        $collections = [
            'mdi',
            'material-symbols',
            'material-symbols-light',
            'ic',
            'ph',
            'solar',
            'tabler',
            'hugeicons',
            'fluent',
            'heroicons',
            'arcticons',
            'openmoji',
            'game-icons',
        ];

        $names = array_unique(array_merge(
            Str::contains($name, ':') ? [$name] : [],
            Arr::map($collections, fn ($collection) => $collection.':'.Str::after($name, ':')),
        ));

        foreach ($names as $name) {
            $cached = Cache::store()->get($this->iconCacheKey($name));

            if ($cached) {
                return $cached;
            }
        }

        foreach ($names as $name) {
            $path = $this->iconStoragePath($name);

            if (File::exists($path)) {
                $contents = File::get($path);

                Cache::store()->rememberForever($this->iconCacheKey($name), fn () => $contents);

                return $contents;
            }
        }

        foreach ($names as $name) {
            $response = Http::get("https://api.iconify.design/{$name}.svg");

            if (! $response->successful()) {
                continue;
            }

            $contents = $response->body();

            if (Str::doesntContain($contents, '<svg', true)) {
                continue;
            }

            $path = $this->iconStoragePath($name);

            File::ensureDirectoryExists(dirname($path));
            File::put($path, $contents);

            Cache::store()->rememberForever($this->iconCacheKey($name), fn () => $contents);

            return $contents;
        }

        return null;
    }
}
