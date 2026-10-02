<?php

namespace TALLKit\Livewire;

use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class DateRangeSynth extends Synth
{
    public static $key = 'tkdr';

    public static function match($target)
    {
        return $target instanceof DateRange;
    }

    public static function matchByType($type)
    {
        return $type === DateRange::class;
    }

    public static function unwrapForValidation($target)
    {
        return static::toArray($target);
    }

    public static function hydrateFromType($type, $value)
    {
        return static::fromArray($value);
    }

    public function dehydrate($target, $dehydrateChild)
    {
        return [static::toArray($target), []];
    }

    public function hydrate($value, $meta)
    {
        return static::fromArray($value);
    }

    public function set(&$target, $key, $value, $property = null)
    {
        $target = match ($key) {
            'start' => static::unreadable($value) ? $target : DateRange::between($value, $target->end()),
            'end' => static::unreadable($value) ? $target : DateRange::between($target->start(), $value),
            'preset' => $this->withPreset($target, $value, $property),
            default => $target,
        };
    }

    // From the browser only text is a date: a number would be read as a timestamp (123 → 1970-01-01).
    protected static function unreadable($value): bool
    {
        return filled($value) && (! is_string($value) || DateRange::parseDate($value) === null);
    }

    protected static function text(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }

    public function unset(&$target, $key)
    {
        $target = match ($key) {
            'start' => null,
            'end' => DateRange::between($target->start()),
            'preset' => DateRange::between($target->start(), $target->end()),
            default => $target,
        };
    }

    protected static function toArray(DateRange $target): array
    {
        $data = [
            'start' => $target->start()?->format('Y-m-d'),
            'end' => $target->end()?->format('Y-m-d'),
        ];

        if ($preset = $target->preset()) {
            $data['preset'] = $preset->value;
        }

        return $data;
    }

    protected static function fromArray($value): ?DateRange
    {
        if (is_string($value)) {
            [$start, $end] = array_pad(explode('/', $value, 2), 2, null);
            $value = ['start' => $start, 'end' => $end];
        }

        if (! is_array($value)) {
            return null;
        }

        $start = static::text($value['start'] ?? null);
        $end = static::text($value['end'] ?? null);

        return match ($preset = DateRangePreset::tryFrom(static::text($value['preset'] ?? null) ?? '')) {
            null => DateRange::between($start, $end),
            DateRangePreset::Custom => DateRange::custom($start, $end),
            DateRangePreset::AllTime => blank($start) ? null : DateRange::allTime($start),
            default => DateRange::fromPreset($preset),
        };
    }

    protected function withPreset(DateRange $target, $value, $property): ?DateRange
    {
        return match ($preset = DateRangePreset::tryFrom(static::text($value) ?? '')) {
            null => $target,
            DateRangePreset::Custom => DateRange::custom($target->start(), $target->end()),
            DateRangePreset::AllTime => DateRange::allTime($this->allTimeStart($property) ?? $target->start()),
            default => DateRange::fromPreset($preset),
        };
    }

    protected function allTimeStart($property)
    {
        $component = $this->context->component;

        return method_exists($component, 'dateRangeAllTimeStart') ? $component->dateRangeAllTimeStart($property) : null;
    }
}
