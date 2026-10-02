<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait InteractsWithUrl
{
    public function routeDetect(array|string|null $routes, $parameters = null, ?string $default = '/'): ?string
    {
        foreach (array_filter(Arr::wrap($routes)) as $route) {
            if (Route::has($route)) {
                return route($route, $parameters);
            }
        }

        return $default;
    }

    public function findAsset(array|string $paths): ?string
    {
        foreach (array_filter(Arr::wrap($paths)) as $path) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return null;
    }

    public function findImage(string $name, array $dirs = ['', 'imgs/', 'images/'], array $exts = ['png', 'jpg', 'jpeg']): ?string
    {
        $paths = collect($dirs)->flatMap(fn ($dir) => collect($exts)->map(fn ($ext) => "{$dir}{$name}.{$ext}"))->all();

        return $this->findAsset($paths);
    }

    // Read as a browser reads an href: "\tjava\nscript:" is javascript:, and parse_url() takes " javascript:" for a path.
    public function safeUrl(?string $url): ?string
    {
        $url = preg_replace('/[\t\n\r]/', '', trim((string) $url, "\x00..\x20"));

        if ($url === '') {
            return null;
        }

        if (preg_match('/^([^:\/?#]+):/', $url, $match)) {
            return in_array(strtolower($match[1]), ['http', 'https'], true) ? $url : null;
        }

        return $url;
    }

    public function isCurrentHref(?string $href = null, ?bool $exact = null, ?bool $query = null): bool
    {
        $path = $this->hrefPath($href);

        if ($path === null) {
            return false;
        }

        $pattern = $exact || $path === '/' ? $path : [$path, "$path/*"];

        if ($query !== false) {
            parse_str((string) parse_url((string) $href, PHP_URL_QUERY), $wanted);

            $current = $wanted ? $this->livewireOriginalQuery() : [];

            foreach ($wanted as $key => $value) {
                $actual = $current[$key] ?? null;
                $same = is_array($value) ? $actual == $value : is_scalar($actual) && (string) $actual === (string) $value;

                if (! $same) {
                    return false;
                }
            }
        }

        if ($this->livewireRequest()) {
            return Str::is($pattern, rawurldecode($this->livewireOriginalPath()));
        }

        return request()->is($pattern);
    }

    protected function hrefPath(?string $href): ?string
    {
        if (blank($href)) {
            return null;
        }

        $host = parse_url($href, PHP_URL_HOST);

        if ($host && ! in_array($host, [request()->getHost(), parse_url((string) config('app.url'), PHP_URL_HOST)], true)) {
            return null;
        }

        // Decoded: route() percent-encodes non-ASCII paths.
        $path = rawurldecode((string) parse_url($href, PHP_URL_PATH));

        if ($path === '' && ! $host) {
            return null;
        }

        $path = $this->withoutBasePath(trim($path, '/'));

        return $path === '' ? '/' : $path;
    }

    protected function withoutBasePath(string $path): string
    {
        $basePath = trim((string) parse_url((string) config('app.url'), PHP_URL_PATH), '/');

        if ($basePath !== '' && ($path === $basePath || Str::startsWith($path, $basePath.'/'))) {
            return trim(Str::after($path, $basePath), '/');
        }

        return $path;
    }
}
