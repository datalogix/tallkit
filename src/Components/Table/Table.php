<?php

namespace TALLKit\Components\Table;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
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
        public ?bool $sortable = null,
        public ?bool $dense = null,
        public ?bool $card = null,
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
        if (! is_string($this->resource)) {
            return $this->resource;
        }

        return make_model($this->resource)?->all();
    }

    protected function parseColsAndRows()
    {
        $cols = collect($this->cols);
        $rows = collect($this->rows ?? $this->parseResource());
        $displayIdColumn = $this->displayIdColumn;

        if ($cols->isEmpty() && $rows->isNotEmpty()) {
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

    public function getRowValue($row, $col)
    {
        return function () use ($row, $col) {
            $value = data_get($row, "{$col}_formatted", data_get($row, $col));

            if ($value instanceof EloquentCollection) {
                $value = $value->map(fn ($item) => data_get($item, 'name', data_get($item, 'title', data_get($item, 'text', $item))));
            }

            if ($value instanceof Arrayable) {
                $value = $value->toArray();
            }

            if ($value instanceof Jsonable) {
                $value = $value->toJson();
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

            if (is_array($value)) {
                $value = implode('<br />', $value);
            }

            if (is_string($value)) {
                $value = nl2br($value);
            }

            return $value;
        };
    }
}
