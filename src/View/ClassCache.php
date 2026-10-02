<?php

namespace TALLKit\View;

use Composer\InstalledVersions;
use TailwindMerge\TailwindMerge;

/** tailwind-merge's own cache is the app's store: a query per call with a database store. */
class ClassCache
{
    protected static array $merged = [];

    protected static array $pending = [];

    protected static bool $loaded = false;

    protected static ?TailwindMerge $merger = null;

    protected static ?string $fingerprint = null;

    // A file this big has gone wrong: it isn't written any more.
    protected const MAX_ENTRIES = 20000;

    public static function merge(string $added, string $current): string
    {
        $key = static::key($added, $current);

        if (($merged = static::get($key)) !== null) {
            return $merged;
        }

        // Without a cache: misses would still hit the app's store.
        static::$merger ??= TailwindMerge::factory()->withConfiguration(config('tailwind-merge', []))->make();

        static::put($key, $merged = static::$merger->merge($added, $current));

        return $merged;
    }

    protected static function get(string $key): ?string
    {
        if (! static::$loaded) {
            static::load();
        }

        return static::$merged[$key] ?? null;
    }

    protected static function put(string $key, string $classes): void
    {
        // A long-lived process (Octane, a queue worker) doesn't grow it forever.
        if (\count(static::$merged) >= static::MAX_ENTRIES * 2) {
            static::$merged = [];
            static::$loaded = false;
        }

        static::$merged[$key] = $classes;

        if (\count(static::$pending) < static::MAX_ENTRIES) {
            static::$pending[$key] = $classes;
        }
    }

    protected static function key(string $added, string $current): string
    {
        return hash('xxh128', $added."\n".$current);
    }

    public static function path(): ?string
    {
        if (! config('tallkit.cache_classes', true)) {
            return null;
        }

        $directory = config('view.compiled');

        return \is_string($directory) && $directory !== '' ? $directory.'/tallkit-classes.php' : null;
    }

    protected static function load(): void
    {
        static::$loaded = true;
        static::$merged = static::read() + static::$merged;
    }

    protected static function read(): array
    {
        $path = static::path();

        if ($path === null || ! is_file($path)) {
            return [];
        }

        $file = @include $path;

        return \is_array($file) && ($file['fingerprint'] ?? null) === static::fingerprint() && \is_array($file['classes'] ?? null)
            ? $file['classes']
            : [];
    }

    protected static function fingerprint(): string
    {
        if (static::$fingerprint !== null) {
            return static::$fingerprint;
        }

        $package = 'gehrisandro/tailwind-merge-php';
        $version = InstalledVersions::isInstalled($package) ? InstalledVersions::getVersion($package) : null;

        return static::$fingerprint = hash('xxh128', json_encode([config('tailwind-merge', []), $version]) ?: '');
    }

    // Written to a temporary file and renamed over it: a request never reads half a file.
    public static function persist(): void
    {
        $path = static::path();

        if ($path === null || static::$pending === [] || ! is_dir(\dirname($path))) {
            static::$pending = [];

            return;
        }

        $merged = static::read() + static::$pending;
        static::$pending = [];

        if (\count($merged) > static::MAX_ENTRIES) {
            return;
        }

        $temporary = $path.'.'.bin2hex(random_bytes(6)).'.tmp';

        if (@file_put_contents($temporary, '<?php return '.var_export(['fingerprint' => static::fingerprint(), 'classes' => $merged], true).';'.PHP_EOL) === false) {
            return;
        }

        if (! @rename($temporary, $path)) {
            @unlink($temporary);

            return;
        }

        if (\function_exists('opcache_invalidate')) {
            @opcache_invalidate($path, true);
        }

        static::$merged = $merged + static::$merged;
    }

    public static function flush(): void
    {
        static::$merged = [];
        static::$pending = [];
        static::$loaded = false;
        static::$merger = null;
        static::$fingerprint = null;
    }
}
