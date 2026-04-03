<?php

namespace TALLKit\Concerns;

use BackedEnum;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

trait InteractsWithTable
{
    public function getRowValue($row, $key, $col)
    {
        $value = $this->extractValue($row, $key, $col);
        $value = $this->resolveRelationValue($value, $key);
        $value = $this->formatDateTimeValue($value);
        $value = $this->formatEnumValue($value);
        $value = $this->formatSerializableValue($value);
        $value = $this->formatArrayValue($value);
        $value = $this->formatStringValue($value);
        $value = $this->applyTranslation($value, $col);

        return $value;
    }

    protected function extractValue($row, $key, $col)
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

    protected function resolveRelationValue(mixed $value, $key)
    {
        if ($value instanceof Model) {
            return $this->extractPropertyFromModel($value, $key);
        }

        if ($value instanceof Collection) {
            return $value->map(fn ($item) => $this->extractPropertyFromModel($item, $key));
        }

        return $value;
    }

    protected function extractPropertyFromModel(Model $model, $key)
    {
        return data_get(
            $model,
            $key,
            fn () => $this->findPropertyValue($model, ['name', 'title', 'text'])
        );
    }

    protected function findPropertyValue($object, array $properties)
    {
        foreach ($properties as $property) {
            $value = data_get($object, $property);

            if ($value !== null) {
                return $value;
            }
        }

        return $object;
    }

    protected function formatDateTimeValue(mixed $value)
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

    protected function formatEnumValue(mixed $value)
    {
        if ($value instanceof BackedEnum) {
            return method_exists($value, 'label') ? $value->label() : $value->value;
        }

        if ($value instanceof UnitEnum) {
            return $value->name;
        }

        return $value;
    }

    protected function formatSerializableValue(mixed $value)
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

    protected function formatArrayValue(mixed $value)
    {
        if (is_array($value)) {
            return implode('<br />', $value);
        }

        return $value;
    }

    protected function formatStringValue(mixed $value)
    {
        if (is_string($value)) {
            return nl2br($value);
        }

        return $value;
    }

    protected function applyTranslation(mixed $value, $col)
    {
        if (($col['translate'] ?? false) && is_string($value)) {
            return __($value);
        }

        return $value;
    }
}
