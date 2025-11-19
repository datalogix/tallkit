<?php

namespace TALLKit\Components\Table;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\View\ComponentSlot;
use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Table extends BladeComponent
{
    public function __construct(
        public mixed $resource = null,
        public mixed $rows = null,
        public mixed $cols = null,
        public ?bool $pagination = null,
        public ?bool $sortable = null,
        public ?bool $border = null,
        public ?bool $dense = null,
        public ?bool $stripped = null,
        public ?bool $hover = null,
        public ?bool $verticalLines = null,
        public ?bool $horizontalLines = null,
        public ?bool $sticky = null,
        public null|bool|ComponentSlot $rowSelection = null,
        public null|bool|ComponentSlot $selectAll = null,
        public ?string $rowKey = null,
        public string|ComponentSlot|null $noRecords = null,
        public string|ComponentSlot|null $footer = null,
        public ?bool $displayIdColumn = null,
        public ?bool $mapRelationsColumn = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        [$this->cols, $this->rows] = $this->parseColsAndRows();
    }

    protected function parseResource()
    {
        return is_string($this->resource)
            ? make_model($this->resource)
            : $this->resource;
    }

    protected function parseRows()
    {
        $rows = $this->rows ?? $this->parseResource();

        return match (true) {
            $rows instanceof Model => $rows->paginate(),
            $rows instanceof Builder => $rows->paginate(),
            $rows instanceof Paginator => $rows,
            $rows instanceof CursorPaginator => $rows,
            $rows === null => null,
            default => collect($rows),
        };
    }

    protected function parseColsAndRows()
    {
        $cols = collect($this->cols);
        $rows = $this->parseRows();

        $displayIdColumn = $this->displayIdColumn;

        if ($cols->isEmpty() && $rows?->isNotEmpty()) {
            $cols = collect($rows->first())->keys();
        } elseif ($displayIdColumn === null) {
            $displayIdColumn = true;
        }

        $cols = $cols->mapWithKeys(function ($value, $key) {
            $name = data_get($value, 'name', is_array($value) ? $key : $value);
            $newKey = Str::snake(is_numeric($key) ? $name : $key);

            return [
                $newKey => [
                    '_key' => $key,
                    'sortable' => data_get($value, 'sortable', $this->sortable),
                    'name' => Str::before($name, '.'),
                ] + (is_array($value) ? $value : []),
            ];
        });

        if (! $displayIdColumn) {
            $cols = $cols->filter(fn ($col, $key) => mb_strtolower($key) !== 'id');
        }

        if ($this->mapRelationsColumn ?? true) {
            $mappedRelations = [];

            $cols = $cols->mapWithKeys(function ($col, $key) use (&$mappedRelations) {
                if (in_array($key, $mappedRelations)) {
                    return null;
                }

                if (Str::endsWith($key, '_id')) {
                    array_push($mappedRelations, $col);

                    return [Str::replaceLast('_id', '', $key) => $col];
                }

                return [$key => $col];
            })->filter();
        }

        return [
            $cols,
            $rows,
        ];
    }

    public function getRowValue($row, $key, $col)
    {
        return function () use ($row, $key, $col) {
            $value = data_get($row, "{$key}_formatted", data_get($row, $key));

            if ($value instanceof Model) {
                $value = data_get($value, $key, data_get($value, 'name', data_get($value, 'title', data_get($value, 'text', $value))));
            }

            if ($value instanceof EloquentCollection) {
                $value = $value->map(fn ($item) => data_get($value, $key, data_get($item, 'name', data_get($item, 'title', data_get($item, 'text', $item)))));
            }

            if ($value instanceof CarbonInterface) {
                if ($value->toDateString() === '1970-01-01') {
                    $value = $value->isoFormat('LT');
                } elseif ($value->toTimeString() === '00:00:00') {
                    $value = $value->isoFormat('L');
                } else {
                    $value = $value->isoFormat('L LT');
                }
            }

            if (is_object($value) && method_exists($value, 'format')) {
                $value = $value->format();
            }

            if ($value instanceof Arrayable) {
                $value = $value->toArray();
            }

            if ($value instanceof Jsonable) {
                $value = $value->toJson();
            }

            if (is_array($value)) {
                $value = implode('<br />', $value);
            }

            if (is_string($value)) {
                $value = nl2br($value);
            }

            if ($value instanceof \BackedEnum) {
                $value = $value->value;
            }

            if ($value instanceof \UnitEnum) {
                $value = $value->name;
            }

            if ($col['translate'] ?? false) {
                $value = __($value);
            }

            return $value;
        };
    }
}
