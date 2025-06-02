<?php

namespace TALLKit\Components\Icon;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Icon extends BladeComponent
{
    public static $collections = ['mdi', 'ph'];

    public function __construct(
        public ?string $size = null,
        public ?string $icon = null,
        public ?string $name = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->svg = null;
        $this->setVariables(['svg']);

        $iconName = $this->name ?? $this->icon;

        if (! $iconName) {
            return;
        }

        $names = array_unique(array_merge(
            str_contains($iconName, ':') ? [$iconName] : [],
            Arr::map(static::$collections, fn ($collection) => $collection.':'.str($iconName)->after(':')),
        ));

        foreach ($names as $name) {
            if (! $this->svg) {
                $this->svg = static::getOrFetchSvg($name);
            }
        }
    }

    protected static function getOrFetchSvg(string $name)
    {
        $path = storage_path('app/tallkit/icons/'.str($name)->snake().'.svg');

        if (File::exists($path)) {
            return File::get($path);
        }

        $url = "https://api.iconify.design/{$name}.svg";
        $response = Http::get($url);

        if (! $response->successful()) {
            return null;
        }

        $contents = $response->body();

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $contents);

        return $contents;
    }
}
