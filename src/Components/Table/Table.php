<?php

namespace TALLKit\Components\Table;

use Illuminate\Support\Str;
use TALLKit\View\BladeComponent;

class Table extends BladeComponent
{
    protected function props()
    {
        return [
            'stripped' => null,
            'hover' => null,
            'rows' => null,
            'cols' => null,
            'empty' => null,
        ];
    }

    protected function mounted(array $data)
    {
        $this->rows = static::parseRows($this->rows);
        $this->cols = static::parseCols($this->cols, $this->rows);
    }

    protected static function parseRows($rows)
    {
        return collect($rows);
    }

    protected static function parseCols($cols, $rows)
    {
        $cols = collect($cols);
        $rows = collect($rows);

        if ($cols->isEmpty() && $rows->isNotEmpty()) {
            $cols = collect($rows->first())->keys();
        }

        return $cols->mapWithKeys(function ($value, $key) {
            $name = data_get($value, 'name', is_array($value) ? $key : $value);
            $newKey = Str::snake(is_numeric($key) ? $name : $key);

            return [$newKey => [
                '_key' => $key,
                'sortable' => data_get($value, 'sortable', false),
                'name' => $name,
            ] + (is_array($value) ? $value : [])];
        });
    }

    public static function getRowValue($row, $col)
    {
        if ($value = data_get($row, $col)) {
            if (is_array($value)) {
                $value = data_get($value, 'name', data_get($value, 'title', data_get($value, 'text', json_encode($value))));
            }

            return $value;
        }

        foreach (['snake', 'kebab', 'studly'] as $function) {
            if ($value = data_get($row, Str::$function($col))) {
                return $value;
            }
        }

        return null;
    }
}
