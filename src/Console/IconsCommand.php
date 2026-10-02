<?php

namespace TALLKit\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use TALLKit\Facades\TALLKit;

class IconsCommand extends Command
{
    protected $signature = 'tallkit:icons
        {icons?* : Icon names to fetch, besides the ones found in the views}
        {--path=* : More folders of views to look in}
        {--missing : Only list the icons that can\'t be found (fails if there are any)}
        {--bundle : For the package itself: fetch the icons its own views use into resources/icons (shipped with it)}';

    protected $description = 'Fetch the icons the views use ahead of time, so no page waits on iconify for them';

    public function handle(): int
    {
        if ($this->option('bundle')) {
            return $this->bundle();
        }

        $names = collect($this->argument('icons'))
            ->merge($this->iconsInViews($this->viewPaths()))
            ->filter(fn ($name) => TALLKit::iconNameIsValid($name))
            ->unique()
            ->sort()
            ->values();

        if ($names->isEmpty()) {
            $this->components->info('No icons found.');

            return self::SUCCESS;
        }

        TALLKit::iconRebuildManifest();

        $results = TALLKit::iconLookup($names->all(), retry: true);

        $missing = array_keys(array_filter($results, fn ($result) => $result['status'] === 'missing'));
        $failed = array_keys(array_filter($results, fn ($result) => $result['status'] === 'failed'));

        if ($this->option('missing')) {
            foreach ($missing as $name) {
                $this->line($name);
            }
        } else {
            foreach ($results as $name => $result) {
                $this->components->twoColumnDetail($name, match ($result['status']) {
                    'found' => '<fg=green>ok</>',
                    'missing' => '<fg=red>not found</>',
                    default => '<fg=yellow>iconify did not answer</>',
                });
            }
        }

        $this->newLine();
        $this->components->info(sprintf('%d icons, %d not found. %d in %s.', $names->count(), count($missing), count(TALLKit::iconManifest()), str_replace(base_path().DIRECTORY_SEPARATOR, '', TALLKit::iconManifestPath())));

        if ($failed !== []) {
            $this->components->warn(sprintf('%d icons could not be checked: iconify did not answer. Run the command again in a while.', count($failed)));
        }

        return ($this->option('missing') && $missing !== []) || $failed !== [] ? self::FAILURE : self::SUCCESS;
    }

    protected function bundle(): int
    {
        $views = __DIR__.'/../../resources/views';

        $names = collect($this->iconsInViews([$views]))
            ->merge($this->prefixedNamesInViews($views))
            ->merge(collect(array_keys(TALLKit::uploadFileTypes()))->map(fn ($type) => "ph:file-{$type}")->push('ph:file'))
            ->filter(fn ($name) => TALLKit::iconNameIsValid($name))
            ->map(fn ($name) => Str::lower($name))
            ->unique()
            ->sort()
            ->values();

        $results = TALLKit::iconLookup($names->all(), retry: true, refresh: true);
        $notFound = array_keys(array_filter($results, fn ($result) => $result['status'] !== 'found'));

        // All or nothing: a partial bundle would send those icons to iconify for every app.
        if ($notFound !== []) {
            $this->components->error('Not written, these could not be fetched: '.implode(', ', $notFound));

            return self::FAILURE;
        }

        $directory = realpath(dirname(TALLKit::iconBundledPath())) ?: dirname(TALLKit::iconBundledPath());
        File::ensureDirectoryExists($directory);

        File::put(TALLKit::iconBundledPath(), implode(PHP_EOL, [
            '<?php',
            '',
            '// The icons the package\'s own views use, so its components need no network. Made by',
            '// `php artisan tallkit:icons --bundle` from iconify: don\'t edit it, run that again. Licenses: LICENSES.md.',
            '',
            'return [',
            ...collect($results)->map(fn ($result, $name) => '    '.var_export($name, true).' => '.var_export($result['svg'], true).',')->values(),
            '];',
            '',
        ]));

        $byCollection = collect($results)->groupBy(fn ($result) => $result['collection'], preserveKeys: true)->map->keys();
        $collections = Http::timeout(10)->get('https://api.iconify.design/collections', ['prefixes' => $byCollection->keys()->implode(',')])->json() ?? [];

        File::put($directory.'/LICENSES.md', collect([
            '# Icon licenses',
            '',
            'The icons in `icons.php` come from these collections, through [Iconify](https://iconify.design). Each keeps its own license.',
        ])->merge($byCollection->sortKeys()->flatMap(fn ($icons, $prefix) => [
            '',
            sprintf('## %s (`%s`)', $collections[$prefix]['name'] ?? $prefix, $prefix),
            '',
            sprintf('- Author: [%s](%s)', $collections[$prefix]['author']['name'] ?? '?', $collections[$prefix]['author']['url'] ?? ''),
            sprintf('- License: [%s](%s)', $collections[$prefix]['license']['title'] ?? '?', $collections[$prefix]['license']['url'] ?? ''),
            '- Icons: '.$icons->map(fn ($icon) => "`{$icon}`")->implode(', '),
        ]))->implode(PHP_EOL).PHP_EOL);

        $this->components->info(sprintf('%d icons from %d collections in %s.', count($results), $byCollection->count(), $directory));

        return self::SUCCESS;
    }

    protected function prefixedNamesInViews(string $path): array
    {
        $collections = array_keys(Http::timeout(10)->get('https://api.iconify.design/collections')->json() ?? []);
        $names = [];

        foreach (File::allFiles($path) as $file) {
            preg_match_all("/['\"]([a-z0-9]+(?:-[a-z0-9]+)*):([a-z0-9]+(?:-[a-z0-9]+)*)['\"]/", $file->getContents(), $matches, PREG_SET_ORDER);

            foreach ($matches as [, $collection, $icon]) {
                if (in_array($collection, $collections, true)) {
                    $names[] = "{$collection}:{$icon}";
                }
            }
        }

        return $names;
    }

    protected function quotedValues(string $expression): array
    {
        preg_match_all("/'([^']*)'/", $expression, $matches, PREG_OFFSET_CAPTURE);

        return collect($matches[0])
            ->filter(function (array $match) use ($expression) {
                [$text, $at] = $match;
                $before = rtrim(substr($expression, 0, $at));
                $after = ltrim(substr($expression, $at + strlen($text)));

                return ! Str::endsWith($before, ['[', '(']) && ! Str::startsWith($after, ['=>', ']']);
            })
            ->map(fn (array $match) => trim($match[0], "'"))
            ->filter()
            ->values()
            ->all();
    }

    protected function viewPaths(): array
    {
        return collect([__DIR__.'/../../resources/views', resource_path('views'), ...$this->option('path')])
            ->filter(fn ($path) => is_dir($path))
            ->all();
    }

    protected function iconsInViews(array $paths): array
    {
        $names = [];

        foreach ($paths as $path) {
            foreach (File::allFiles($path) as $file) {
                if (! str_ends_with($file->getFilename(), '.php')) {
                    continue;
                }

                $contents = $file->getContents();

                preg_match_all('/(?<![\w:-])icon(?:-?(?:trailing|leading|on|off|Trailing|Leading|On|Off))?="([^"{$]+)"/', $contents, $attributes);
                preg_match_all('/<(?:tk[:-]|x-tallkit::)icon\b(?:[^>"\']|"[^"]*"|\'[^\']*\')*?\bname="([^"{$]+)"/', $contents, $tags);
                preg_match_all('/[\'"]icon(?:Trailing|On|Off)?[\'"]\s*=>\s*(?:\$[\w\->\[\]\'"]+\s*(?:\?\?|\?:)\s*)?\'([^\']+)\'|\bicon:\s*\'([^\']+)\'/', $contents, $arrays);
                preg_match_all('/(?<![\w-]):icon(?:-?(?:trailing|leading|on|off|Trailing|Leading|On|Off))?="([^"]*)"/', $contents, $bound);

                $quoted = collect($bound[1])->flatMap(fn ($expression) => $this->quotedValues($expression));

                array_push($names, ...$attributes[1], ...$tags[1], ...array_filter($arrays[1]), ...array_filter($arrays[2]), ...$quoted);
            }
        }

        return $names;
    }
}
