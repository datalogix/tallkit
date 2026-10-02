<?php

namespace TALLKit\Concerns;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

trait InteractsWithIcon
{
    protected static ?array $iconManifest = null;

    protected static ?array $iconBundled = null;

    protected function iconKey(string $name)
    {
        return $this->storageKey('icon', $name);
    }

    protected function iconStoragePath(string $name)
    {
        // A folder per collection: ":" isn't allowed in Windows file names.
        return storage_path('app/tallkit/icons/'.str_replace(':', '/', Str::lower($name)).'.svg');
    }

    public function iconNameIsValid(string $name): bool
    {
        return (bool) preg_match('/^(?:[a-z0-9]+(?:-[a-z0-9]+)*:)?[a-z0-9]+(?:[-_][a-z0-9]+)*$/i', $name);
    }

    public function iconSvg(?string $name, bool $retry = false): ?string
    {
        // The name goes into a URL, a cache key and a file path, and may come from data.
        if (! $name || ! $this->iconNameIsValid($name)) {
            return null;
        }

        $seen = app('tallkit.icons');

        if (! $retry && $seen->offsetExists($name)) {
            return $seen[$name];
        }

        return $seen[$name] = $this->iconLookup([$name], $retry)[$name]['svg'];
    }

    protected function iconCollections(): array
    {
        return (array) config('tallkit.icon.collections', ['mdi', 'material-symbols', 'material-symbols-light', 'ic', 'ph', 'solar', 'tabler', 'hugeicons', 'fluent', 'heroicons', 'arcticons', 'openmoji', 'game-icons']);
    }

    public function iconLookup(array $names, bool $retry = false, bool $refresh = false): array
    {
        $store = Cache::store();
        $manifest = $refresh ? [] : $this->iconManifest();
        $bundled = $refresh ? [] : $this->iconBundled();
        $results = [];
        $wanted = [];
        $learned = [];

        foreach (array_unique($names) as $name) {
            if (! $this->iconNameIsValid($name)) {
                $results[$name] = ['status' => 'missing', 'svg' => null];

                continue;
            }

            $key = Str::lower($name);

            if (isset($manifest[$key])) {
                $results[$name] = ['status' => 'found', 'svg' => $manifest[$key]];

                continue;
            }

            if (isset($bundled[$key])) {
                $results[$name] = ['status' => 'found', 'svg' => $bundled[$key]];

                continue;
            }

            $candidates = $this->iconCandidates($name);
            $svg = $refresh ? null : $this->iconStored($candidates);

            if ($svg !== null) {
                $results[$name] = ['status' => 'found', 'svg' => $svg];
                $learned[$key] = $svg;
            } elseif (! $retry && ! $refresh && $store->has($this->iconMissingKey($name))) {
                $results[$name] = ['status' => 'missing', 'svg' => null];
            } else {
                $wanted[$name] = $candidates;
            }
        }

        if ($wanted !== [] && ! $retry && ! $refresh && $store->has($this->iconKey('unavailable'))) {
            $results += array_map(fn () => ['status' => 'failed', 'svg' => null], $wanted);
            $wanted = [];
        }

        if ($wanted !== []) {
            [$sets, $failed] = $this->iconFetchSets($wanted);

            if ($failed !== []) {
                $store->put($this->iconKey('unavailable'), true, 60);
            }

            foreach ($wanted as $name => $candidates) {
                $results[$name] = $this->iconPick($name, $candidates, $sets, $failed);

                if ($results[$name]['svg'] !== null) {
                    $learned[Str::lower($name)] = $results[$name]['svg'];
                }
            }
        }

        if ($learned !== []) {
            $this->iconRemember($learned);
        }

        return $results;
    }

    public function iconBundledPath(): string
    {
        return __DIR__.'/../../resources/icons/icons.php';
    }

    protected function iconBundled(): array
    {
        if (static::$iconBundled === null) {
            $path = $this->iconBundledPath();

            static::$iconBundled = is_file($path) ? (array) (include $path) : [];
        }

        return static::$iconBundled;
    }

    public function iconManifestPath(): string
    {
        return storage_path('app/tallkit/icons/manifest.php');
    }

    public function iconManifest(bool $fresh = false): array
    {
        if ($fresh || static::$iconManifest === null) {
            $path = $this->iconManifestPath();

            if ($fresh && function_exists('opcache_invalidate')) {
                opcache_invalidate($path, true);
            }

            static::$iconManifest = is_file($path) ? (array) (include $path) : [];
        }

        return static::$iconManifest;
    }

    protected function iconRemember(array $icons): void
    {
        $path = $this->iconManifestPath();

        File::ensureDirectoryExists(dirname($path));

        $lock = fopen($path.'.lock', 'c');

        try {
            flock($lock, LOCK_EX);

            $icons = [...$this->iconManifest(fresh: true), ...$icons];
            ksort($icons);

            $temporary = $path.'.'.Str::random(8).'.tmp';
            File::put($temporary, '<?php'.PHP_EOL.PHP_EOL.'return '.var_export($icons, true).';'.PHP_EOL);
            rename($temporary, $path);

            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($path, true);
            }

            static::$iconManifest = $icons;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    public function iconRebuildManifest(): int
    {
        $icons = [];
        $root = dirname($this->iconManifestPath());

        foreach (File::isDirectory($root) ? File::allFiles($root) : [] as $file) {
            if ($file->getExtension() === 'svg') {
                $icons[Str::before($file->getRelativePathname(), '.svg')] = $file->getContents();
            }
        }

        $icons = collect($icons)->mapWithKeys(fn ($svg, $file) => [str_replace(DIRECTORY_SEPARATOR, ':', str_replace('/', ':', $file)) => $svg])->all();

        $path = $this->iconManifestPath();
        File::ensureDirectoryExists(dirname($path));
        File::put($path, '<?php'.PHP_EOL.PHP_EOL.'return '.var_export(Arr::sortRecursive($icons), true).';'.PHP_EOL);

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($path, true);
        }

        static::$iconManifest = $icons;

        return count($icons);
    }

    protected function iconCandidates(string $name): array
    {
        return Str::contains($name, ':')
            ? [Str::lower($name)]
            : Arr::map($this->iconCollections(), fn ($collection) => $collection.':'.Str::lower($name));
    }

    protected function iconMissingKey(string $name): string
    {
        return $this->iconKey('missing:'.$this->iconCandidates($name)[0]);
    }

    protected function iconStored(array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            $path = $this->iconStoragePath($candidate);

            // Sanitized as it's read: a file in the folder may have been put there by hand.
            if (File::exists($path)) {
                return $this->iconSanitize(File::get($path));
            }
        }

        return null;
    }

    protected function iconSanitize(string $svg): ?string
    {
        $document = new \DOMDocument;
        $errors = libxml_use_internal_errors(true);

        try {
            // No LIBXML_NOENT: entities aren't expanded (and a DOCTYPE isn't taken at all).
            $loaded = $document->loadXML($svg, LIBXML_NONET);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($errors);
        }

        if (! $loaded || $document->doctype !== null || $document->documentElement?->localName !== 'svg') {
            return null;
        }

        $xpath = new \DOMXPath($document);
        $changed = false;

        $elements = $xpath->query('//*[contains(" script foreignobject iframe object embed handler listener ", concat(" ", translate(local-name(), "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), " "))]');

        foreach (iterator_to_array($elements) as $element) {
            $element->parentNode->removeChild($element);
            $changed = true;
        }

        foreach (iterator_to_array($xpath->query('//*[translate(local-name(), "STYLE", "style") = "style"]')) as $style) {
            if (preg_match('/@import|url\(\s*[\'"]?\s*(?!#)/i', html_entity_decode($style->textContent))) {
                $style->parentNode->removeChild($style);
                $changed = true;
            }
        }

        foreach (iterator_to_array($xpath->query('//@*')) as $attribute) {
            $name = Str::lower($attribute->localName ?? $attribute->nodeName);
            $value = Str::lower(preg_replace('/[\s\x00-\x1f]+/', '', html_entity_decode($attribute->value)));

            // Anywhere in the value: values="0;javascript:…" applies it on a later step.
            if (Str::startsWith($name, 'on') || Str::contains($value, ['javascript:', 'data:text/html', 'vbscript:'])) {
                $attribute->ownerElement->removeAttributeNode($attribute);
                $changed = true;
            }
        }

        return $changed ? $document->saveXML($document->documentElement) : $svg;
    }

    protected function iconFetchSets(array $wanted): array
    {
        $icons = [];

        foreach ($wanted as $candidates) {
            foreach ($candidates as $candidate) {
                [$collection, $icon] = explode(':', $candidate, 2);
                $icons[$collection][$icon] = true;
            }
        }

        try {
            $responses = Http::pool(fn ($pool) => Arr::map(
                array_keys($icons),
                fn ($collection) => $pool->as($collection)->connectTimeout(2)->timeout(5)
                    ->get("https://api.iconify.design/{$collection}.json", ['icons' => implode(',', array_keys($icons[$collection]))]),
            ));
        } catch (\Throwable $e) {
            report($e);

            $responses = [];
        }

        $sets = [];
        $failed = [];

        foreach (array_keys($icons) as $collection) {
            $response = $responses[$collection] ?? null;

            if (! $response instanceof Response || ! $response->successful()) {
                $failed[$collection] = true;

                continue;
            }

            // A collection iconify doesn't have answers "404" with a 200.
            $set = trim($response->body()) === '404' ? [] : $response->json();

            if (! is_array($set)) {
                $failed[$collection] = true;

                continue;
            }

            $sets[$collection] = $set;
        }

        return [$sets, $failed];
    }

    // A preferred collection that didn't answer leaves it undecided, or a lower one would win for good.
    protected function iconPick(string $name, array $candidates, array $sets, array $failed): array
    {
        foreach ($candidates as $candidate) {
            [$collection, $icon] = explode(':', $candidate, 2);

            if (isset($failed[$collection])) {
                return ['status' => 'failed', 'svg' => null];
            }

            $svg = $this->iconFromSet($sets[$collection] ?? [], $icon, $candidate);

            if ($svg === false) {
                return ['status' => 'failed', 'svg' => null];
            }

            if ($svg !== null && ($svg = $this->iconSanitize($svg)) === null) {
                return ['status' => 'failed', 'svg' => null];
            }

            if ($svg !== null) {
                $path = $this->iconStoragePath($candidate);

                File::ensureDirectoryExists(dirname($path));
                File::put($path, $svg);

                return ['status' => 'found', 'svg' => $svg, 'collection' => $collection];
            }
        }

        Cache::store()->put($this->iconMissingKey($name), true, config('tallkit.icon.missing_ttl', 60 * 60 * 24));

        return ['status' => 'missing', 'svg' => null];
    }

    protected function iconFromSet(array $set, string $icon, string $candidate): string|false|null
    {
        $aliases = $set['aliases'] ?? [];

        for ($depth = 0; ! isset($set['icons'][$icon]) && isset($aliases[$icon]) && $depth < 5; $depth++) {
            if (array_diff(array_keys($aliases[$icon]), ['parent']) !== []) {
                return $this->iconSvgFromApi($candidate);
            }

            $icon = $aliases[$icon]['parent'];
        }

        $data = $set['icons'][$icon] ?? null;

        if (! is_array($data) || ! isset($data['body'])) {
            return null;
        }

        if (array_intersect(array_keys($data), ['rotate', 'hFlip', 'vFlip']) !== []) {
            return $this->iconSvgFromApi($candidate);
        }

        $viewBox = implode(' ', [
            $data['left'] ?? $set['left'] ?? 0,
            $data['top'] ?? $set['top'] ?? 0,
            $data['width'] ?? $set['width'] ?? 16,
            $data['height'] ?? $set['height'] ?? 16,
        ]);

        return '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="'.$viewBox.'">'.$data['body'].'</svg>';
    }

    protected function iconSvgFromApi(string $candidate): string|false
    {
        try {
            $response = Http::connectTimeout(2)->timeout(5)->get("https://api.iconify.design/{$candidate}.svg");
        } catch (\Throwable $e) {
            report($e);

            return false;
        }

        return $response->successful() && Str::contains($response->body(), '<svg', true) ? $response->body() : false;
    }
}
