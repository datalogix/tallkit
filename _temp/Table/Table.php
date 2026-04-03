<?php

namespace TALLKit\Components\Table;

use BackedEnum;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\ComponentSlot;
use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;
use UnitEnum;

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
            $rows instanceof Relation => $rows->paginate(),
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

        $cols = $this->normalizeColumns($cols);
        $cols = $this->filterIdColumn($cols, $displayIdColumn);
        $cols = $this->mapRelationsToColumns($cols);

        return [
            $cols,
            $rows,
        ];
    }

    protected function normalizeColumns(Collection $cols)
    {
        return $cols->filter()->mapWithKeys(function ($value, $key) {
            $name = data_get($value, 'name', is_array($value) ? $key : $value);
            $newKey = Str::snake(is_numeric($key) ? $name : $key);

            return [
                $newKey => [
                    '_key' => $key,
                    'sortable' => data_get($value, 'sortable', $name !== 'actions' && $this->sortable),
                    'name' => Str::before($name, '.'),
                ] + (is_array($value) ? $value : []),
            ];
        });
    }

    protected function filterIdColumn(Collection $cols, ?bool $displayIdColumn = null)
    {
        if ($displayIdColumn) {
            return $cols;
        }

        return $cols->filter(fn ($col, $key) => mb_strtolower($key) !== 'id');
    }

    protected function mapRelationsToColumns(Collection $cols)
    {
        if (! ($this->mapRelationsColumn ?? true)) {
            return $cols;
        }

        $mappedRelations = [];

        return $cols->mapWithKeys(function ($col, $key) use (&$mappedRelations) {
            if (in_array($key, $mappedRelations)) {
                return null;
            }

            if (Str::endsWith($key, '_id')) {
                $mappedRelations[] = $col;

                return [Str::replaceLast('_id', '', $key) => $col];
            }

            return [$key => $col];
        });
    }

    public function getRowValue($row, $key, $col)
    {
        return function () use ($row, $key, $col) {
            $value = $this->extractValue($row, $key, $col);
            $value = $this->resolveRelationValue($value, $key);
            $value = $this->formatDateTimeValue($value);
            $value = $this->formatEnumValue($value);
            $value = $this->formatSerializableValue($value);
            $value = $this->formatArrayValue($value);
            $value = $this->formatStringValue($value);
            $value = $this->applyTranslation($value, $col);

            return $value;
        };
    }

    private function extractValue($row, $key, $col)
    {
        return data_get(
            $row,
            "{$key}_formatted",
            fn () => data_get(
                $row,
                $col['_key'],
                fn () => data_get($row, $key)
            )
        );
    }

    private function resolveRelationValue(mixed $value, $key)
    {
        if ($value instanceof Model) {
            return $this->extractPropertyFromModel($value, $key);
        }

        if ($value instanceof EloquentCollection) {
            return $value->map(fn ($item) => $this->extractPropertyFromModel($item, $key));
        }

        return $value;
    }

    private function extractPropertyFromModel(Model $model, $key)
    {
        return data_get(
            $model,
            $key,
            fn () => $this->findPropertyValue($model, ['name', 'title', 'text'])
        );
    }

    private function findPropertyValue($object, array $properties)
    {
        foreach ($properties as $property) {
            $value = data_get($object, $property);

            if ($value !== null) {
                return $value;
            }
        }

        return $object;
    }

    private function formatDateTimeValue(mixed $value)
    {
        if (! ($value instanceof CarbonInterface)) {
            return $value;
        }

        if ($value->toDateString() === '1970-01-01') {
            return $value->isoFormat('LT');
        }

        if ($value->toTimeString() === '00:00:00') {
            return $value->isoFormat('L');
        }

        return $value->isoFormat('L LT');
    }

    private function formatEnumValue(mixed $value)
    {
        if ($value instanceof BackedEnum) {
            return method_exists($value, 'label') ? $value->label() : $value->value;
        }

        if ($value instanceof UnitEnum) {
            return $value->name;
        }

        return $value;
    }

    private function formatSerializableValue(mixed $value)
    {
        if (is_object($value) && method_exists($value, 'format')) {
            return $value->format();
        }

        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        if ($value instanceof Jsonable) {
            return $value->toJson();
        }

        return $value;
    }

    private function formatArrayValue(mixed $value)
    {
        if (is_array($value)) {
            return implode('<br />', $value);
        }

        return $value;
    }

    private function formatStringValue(mixed $value)
    {
        if (is_string($value)) {
            return nl2br($value);
        }

        return $value;
    }

    private function applyTranslation(mixed $value, $col)
    {
        if (($col['translate'] ?? false) && is_string($value)) {
            return __($value);
        }

        return $value;
    }
}
