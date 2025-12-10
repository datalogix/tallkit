<?php

namespace TALLKit\Components\Icon;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Icon extends BladeComponent
{
    private static $collections = [
        'material-symbols',
        'material-symbols-light',
        'ic',
        'mdi',
        'solar',
        'tabler',
        'hugeicons',
        'fluent',
        'ph',
        'heroicons',
        'arcticons',
        'openmoji',
        'game-icons',
    ];

    public static function setCollections(array $collections)
    {
        static::$collections = $collections;
    }

    public function __construct(
        public ?string $name = null,
        public ?string $icon = null,
        public ?string $size = null,
        public ?string $svg = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $iconName = $this->name ?? $this->icon;

        if (! $iconName) {
            return;
        }

        $names = array_unique(array_merge(
            Str::contains($iconName, ':') ? [$iconName] : [],
            Arr::map(static::$collections, fn ($collection) => $collection.':'.Str::after($iconName, ':')),
        ));

        foreach ($names as $name) {
            if ($this->svg) {
                break;
            }

            $this->svg = static::getOrFetchSvg($name);
        }
    }

    public static function getOrFetchSvg($name)
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
