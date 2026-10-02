<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Str;

trait InteractsWithNaming
{
    public function dataKey(string $name): string
    {
        return 'data-'.self::PREFIX.'-'.$name;
    }

    public function eventName(string $name): string
    {
        return self::PREFIX.':'.$name;
    }

    public function dataSelector(string $name, string|int|null $value = null): string
    {
        return $value === null || $value === ''
            ? '['.$this->dataKey($name).']'
            : '['.$this->dataKey($name).'="'.addcslashes((string) $value, '"\\').'"]';
    }

    public function storageKey(string ...$parts): string
    {
        return implode('.', [self::PREFIX, ...$parts]);
    }

    public function generateId(?string $prefix = null, ?string $name = null, ?string $suffix = null): string
    {
        return collect([
            self::PREFIX,
            Str::slug((string) $prefix),
            $name === null ? Str::lower(Str::random(8)) : $this->idPart($name),
            Str::slug((string) $suffix),
        ])
            ->filter(fn ($value) => $value !== '')
            ->implode('-');
    }

    /** Its slug plus a short hash: "1.5" and "15", "Ação" and "Acao" don't collide, and "日本" isn't empty. */
    public function idPart(string|int $value): string
    {
        $value = (string) $value;
        $slug = Str::slug($value);

        return $slug !== '' && $slug === $value
            ? $slug
            : ltrim($slug.'-', '-').$this->shortHash($value);
    }

    public function shortHash(string $value, int $length = 8): string
    {
        return substr(md5($value), 0, $length);
    }

    public function stableId(string $prefix, ?string $name = null, ?string $suffix = null): string
    {
        // Counted by the resulting id: "a.bc" and "ab.c" would collide.
        $name = $name === null ? null : (Str::slug(str_replace(['.', '[', ']'], '-', $name)) ?: null);
        $scope = $this->livewireRendering() ? $this->livewireComponentId() : null;
        $counts = app('tallkit.ids');
        $key = $prefix.'|'.$scope.'|'.$name.'|'.$suffix;
        $count = $counts[$key] = ($counts[$key] ?? 0) + 1;

        return $this->generateId(
            prefix: $scope ? "{$prefix}-{$scope}" : $prefix,
            name: $name ?? (string) $count,
            suffix: collect([$name !== null && $count > 1 ? $count : null, $suffix])->filter()->implode('-'),
        );
    }
}
