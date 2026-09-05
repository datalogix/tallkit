<?php

namespace TALLKit\Livewire;

use Carbon\Carbon;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class DateRangeSynth extends Synth
{
    public static $key = 'tkdr';

    public static function match($target)
    {
        return is_object($target) && $target instanceof DateRange;
    }

    public static function matchByType($type)
    {
        return $type === DateRange::class;
    }

    public static function unwrapForValidation($target)
    {
        $data = [
            'start' => $target->start()?->format('Y-m-d'),
            'end' => $target->end()?->format('Y-m-d'),
        ];

        $preset = $target->preset();

        $preset && $data['preset'] = $preset->value;

        return $data;
    }

    public static function hydrateFromType($type, $value)
    {
        if ($value === '' || $value === null) {
            return null;
        }

        $preset = $value['preset'] ?? null;

        if ($preset) {
            if ($preset === DateRangePreset::AllTime->value) {
                return DateRange::allTime($value['start']);
            }

            return DateRange::fromPreset(DateRangePreset::from($preset));
        }

        return new DateRange($value['start'] ?? null, $value['end'] ?? null);
    }

    public function dehydrate($target, $dehydrateChild)
    {
        $data = [
            'start' => $target->start()?->format('Y-m-d'),
            'end' => $target->end()?->format('Y-m-d'),
        ];

        $preset = $target->preset();

        $preset && $data['preset'] = $preset->value;

        return [$data, []];
    }

    public function hydrate($value, $meta)
    {
        if ($value === '' || $value === null) {
            return null;
        }

        $preset = $value['preset'] ?? null;

        if ($preset) {
            if ($preset === DateRangePreset::AllTime->value) {
                return DateRange::allTime($value['start']);
            }

            return DateRange::fromPreset(DateRangePreset::from($preset));
        }

        return new DateRange($value['start'] ?? null, $value['end'] ?? null);
    }

    public function set(&$target, $key, $value)
    {
        $target = match ($key) {
            'start' => new DateRange($value, $target->end()),
            'end' => new DateRange($target->start(), Carbon::parse($value)->endOfDay()),
            'preset' => $value === DateRangePreset::AllTime->value
                ? DateRange::allTime($target->start())
                : DateRange::fromPreset(DateRangePreset::from($value)),
        };
    }

    public function unset(&$target, $key)
    {
        $target = match ($key) {
            'start' => new DateRange(null, $target->end()),
            'end' => new DateRange($target->start(), null),
            'preset' => new DateRange($target->start(), $target->end()),
        };
    }
}
