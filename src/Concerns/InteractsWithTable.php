<?php

namespace TALLKit\Concerns;

use BackedEnum;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use Stringable;
use UnitEnum;

trait InteractsWithTable
{
    public function tableColumnAttributes(?string $name, bool $toggleable = false, bool $hidden = false, bool|string|null $sticky = null, bool $pinnable = false): ComponentAttributeBag
    {
        if ($name === null || $name === '') {
            return $this->attributesMerge();
        }

        $attributes = ['data-column-key' => $name];

        if ($hidden) {
            $attributes['hidden'] = true;
            $attributes['data-column-hidden'] = true;
        }

        if ($toggleable) {
            $attributes['x-bind:hidden'] = 'isColumnHidden('.Js::from($name).')';
        }

        if ($sticky === 'left') {
            $attributes['data-column-sticky'] = 'left';
            $attributes['data-column-pinned'] = true;
        }

        if ($pinnable) {
            $attributes['x-bind:data-column-pinned'] = 'isColumnPinned('.Js::from($name).')';
        }

        if ($sticky === 'left' || $pinnable) {
            $attributes['style'] = 'inset-inline-start: var('.$this->tableColumnVar('left', $name).', 0px)';
        }

        return $this->attributesMerge($attributes);
    }

    public function tableSortState(): array
    {
        $query = $this->livewireOriginalQuery();
        $by = $query['sortBy'] ?? null;

        return [is_string($by) && $by !== '' ? $by : null, ($query['sortDirection'] ?? null) === 'desc' ? 'desc' : 'asc'];
    }

    public function tableSortHref(string $column, string $direction): string
    {
        $query = array_merge($this->livewireOriginalQuery(), ['sortBy' => $column, 'sortDirection' => $direction]);

        unset($query['page']);

        return url($this->livewireOriginalPath()).'?'.Arr::query($query);
    }

    public function tableSortQuery($query, $cols): mixed
    {
        [$by, $direction] = $this->tableSortState();

        if ($by === null) {
            return $query;
        }

        $cols = collect($cols)->filter();

        // Only the default columns: any column would let ?sortBy=password (or a $hidden one) order the rows by it.
        $allowed = $cols->isEmpty()
            ? in_array($by, $this->tableDefaultColumns($query->getModel()), true)
            : $cols->contains(function ($value, $key) use ($by) {
                $labelled = ! is_array($value) && ! is_numeric($key);
                $name = data_get($value, 'name', is_array($value) || $labelled ? $key : $value);

                return is_string($name) && $name === $by && ! Str::contains($name, '.') && data_get($value, 'sortable', true) !== false;
            });

        return $allowed ? $query->orderBy($by, $direction) : $query;
    }

    public function tableDefaultColumns(mixed $row): array
    {
        $columns = match (true) {
            $row instanceof Model && $row->getVisible() => $row->getVisible(),
            $row instanceof Model && array_diff($row->getFillable(), $row->getHidden()) => [
                $row->getKeyName(),
                ...array_diff($row->getFillable(), $row->getHidden()),
                $row->usesTimestamps() ? $row->getCreatedAtColumn() : null,
                $row->usesTimestamps() ? $row->getUpdatedAtColumn() : null,
            ],
            $row instanceof Model => array_diff(
                $row->getAttributes() ? array_keys($row->getAttributes()) : Schema::connection($row->getConnectionName())->getColumnListing($row->getTable()),
                $row->getHidden(),
            ),
            default => collect($row)->keys()->all(),
        };

        return collect($columns)
            ->filter(fn ($column) => is_string($column) && $column !== '')
            ->reject(fn ($column) => preg_match('/password|token|secret|api_?key|private_?key|remember|two_factor|recovery_codes?|otp/i', $column))
            ->unique()
            ->values()
            ->all();
    }

    protected function tableColumnVar(string $side, string $name): string
    {
        // Matches the script's /[^\w-]/g, which counts UTF-16 units.
        return "--tk-column-{$side}-".preg_replace_callback(
            '/[^A-Za-z0-9_-]/u',
            fn ($match) => mb_ord($match[0]) > 0xFFFF ? '--' : '-',
            $name,
        );
    }

    public function tableCellValue($row, $key, $col): mixed
    {
        $value = data_get($row, "{$key}_formatted", fn () => data_get($row, $col['_key'] ?? $key, fn () => data_get($row, $key)));

        if ($value === null || is_bool($value) || is_int($value) || is_float($value)) {
            return $value;
        }

        $html = function (mixed $value, bool $inList = false) use (&$html, $key, $col): string {
            return match (true) {
                $value === null => '',
                is_bool($value) => e(__($value ? 'Yes' : 'No')),
                is_int($value), is_float($value) => (string) $value,
                $value instanceof Htmlable => $value->toHtml(),
                // A translation is the developer's text (it may hold HTML); the value isn't.
                is_string($value) => is_string($translated = ($col['translate'] ?? false) ? __($value) : $value) && $translated !== $value
                    ? $translated
                    : nl2br(e($value)),
                // Only its name: a related model of it would chain a query at each step.
                $value instanceof Model => $html(
                    (! $value->isRelation($key) && ! (($own = data_get($value, $key)) instanceof Model || $own instanceof Enumerable) ? $own : null)
                        ?? data_get($value, 'name') ?? data_get($value, 'title') ?? data_get($value, 'text') ?? $value->getKey(),
                    $inList,
                ),
                // Before Arrayable: an Eloquent collection's toArray() turns its models into arrays.
                $value instanceof Enumerable => $html($value->all(), $inList),
                $value instanceof DateTimeInterface => e(Carbon::instance($value)->isoFormat(match (true) {
                    $value->format('Y-m-d') === '1970-01-01' => 'LT',
                    $value->format('H:i:s') === '00:00:00' => 'L',
                    default => 'L LT',
                })),
                $value instanceof UnitEnum => $html(match (true) {
                    method_exists($value, 'label') => $value->label(),
                    $value instanceof BackedEnum => $value->value,
                    default => $value->name,
                }, $inList),
                is_object($value) && method_exists($value, 'format')
                    && (new \ReflectionMethod($value, 'format'))->getNumberOfRequiredParameters() === 0 => $html($value->format(), $inList),
                $value instanceof Arrayable => $html($value->toArray(), $inList),
                $value instanceof Stringable => $html((string) $value, $inList),
                is_array($value) && ! $inList => implode('<br />', array_map(fn ($item) => $html($item, true), $value)),
                default => e((string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
            };
        };

        return $html($value);
    }
}
