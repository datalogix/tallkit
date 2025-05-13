<?php

namespace TALLKit\Components\Icon;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use TALLKit\View\BladeComponent;

class Icon extends BladeComponent
{
    protected function props()
    {
        return [
            'icon' => null,
            'name' => null,
            'size' => null,
        ];
    }

    protected function mounted(array $data)
    {
        $this->svg = str(static::getOrFetchSvg($this->name ?? $this->icon));
        $this->setVariables(['svg']);
    }

    protected static function getOrFetchSvg($name)
    {
        $path = storage_path('app/tallkit/icons/'.str($name)->snake().'.svg');

        if (! $name) {
            return null;
        }

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
