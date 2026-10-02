<?php

namespace TALLKit\Livewire;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;

class DateRange extends CarbonPeriod
{
    protected $preset;

    public function start(): ?CarbonInterface
    {
        return $this->getStartDate();
    }

    public function end(): ?CarbonInterface
    {
        return $this->getEndDate();
    }

    public function bounds(): array
    {
        return [$this->start(), $this->end() ?? $this->start()->copy()->endOfDay()];
    }

    public function preset(): ?DateRangePreset
    {
        return $this->preset;
    }

    public function hasStart(): bool
    {
        return $this->getStartDate() !== null;
    }

    public function hasEnd(): bool
    {
        return $this->getEndDate() !== null;
    }

    public function hasPreset(): bool
    {
        return $this->preset !== null;
    }

    public function isAllTime(): bool
    {
        return $this->preset === DateRangePreset::AllTime;
    }

    public static function between($start, $end = null): ?static
    {
        $start = static::parseDate($start);
        $end = static::parseDate($end);

        if (! $start) {
            return null;
        }

        if ($end && $end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        $start->startOfDay();
        $end?->endOfDay();

        // Without an end it is one day: a period with no end never stops iterating.
        return $end ? new static($start, $end) : new static($start, 1);
    }

    public static function parseDate($value): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function custom($start, $end = null): ?static
    {
        $instance = static::between($start, $end);

        if ($instance) {
            $instance->preset = DateRangePreset::Custom;
        }

        return $instance;
    }

    public static function fromPreset(DateRangePreset $preset): static
    {
        if ($preset === DateRangePreset::AllTime) {
            throw new \InvalidArgumentException('All time date range is not supported via this constructor because it requires a start date. Please use the ::allTime($start) constructor instead.');
        }

        if ($preset === DateRangePreset::Custom) {
            throw new \InvalidArgumentException('Custom date range is not supported via this constructor because it requires its dates. Please use the ::custom($start, $end) constructor instead.');
        }

        $instance = new static(...$preset->dates());

        $instance->preset = $preset;

        return $instance;
    }

    public static function today(): static
    {
        return static::fromPreset(DateRangePreset::Today);
    }

    public static function yesterday(): static
    {
        return static::fromPreset(DateRangePreset::Yesterday);
    }

    public static function thisWeek(): static
    {
        return static::fromPreset(DateRangePreset::ThisWeek);
    }

    public static function lastWeek(): static
    {
        return static::fromPreset(DateRangePreset::LastWeek);
    }

    public static function last7Days(): static
    {
        return static::fromPreset(DateRangePreset::Last7Days);
    }

    public static function thisMonth(): static
    {
        return static::fromPreset(DateRangePreset::ThisMonth);
    }

    public static function lastMonth(): static
    {
        return static::fromPreset(DateRangePreset::LastMonth);
    }

    public static function thisQuarter(): static
    {
        return static::fromPreset(DateRangePreset::ThisQuarter);
    }

    public static function lastQuarter(): static
    {
        return static::fromPreset(DateRangePreset::LastQuarter);
    }

    public static function thisYear(): static
    {
        return static::fromPreset(DateRangePreset::ThisYear);
    }

    public static function lastYear(): static
    {
        return static::fromPreset(DateRangePreset::LastYear);
    }

    public static function last14Days(): static
    {
        return static::fromPreset(DateRangePreset::Last14Days);
    }

    public static function last30Days(): static
    {
        return static::fromPreset(DateRangePreset::Last30Days);
    }

    public static function last3Months(): static
    {
        return static::fromPreset(DateRangePreset::Last3Months);
    }

    public static function last6Months(): static
    {
        return static::fromPreset(DateRangePreset::Last6Months);
    }

    public static function yearToDate(): static
    {
        return static::fromPreset(DateRangePreset::YearToDate);
    }

    public static function tomorrow(): static
    {
        return static::fromPreset(DateRangePreset::Tomorrow);
    }

    public static function nextWeek(): static
    {
        return static::fromPreset(DateRangePreset::NextWeek);
    }

    public static function next7Days(): static
    {
        return static::fromPreset(DateRangePreset::Next7Days);
    }

    public static function nextMonth(): static
    {
        return static::fromPreset(DateRangePreset::NextMonth);
    }

    public static function nextQuarter(): static
    {
        return static::fromPreset(DateRangePreset::NextQuarter);
    }

    public static function nextYear(): static
    {
        return static::fromPreset(DateRangePreset::NextYear);
    }

    public static function next14Days(): static
    {
        return static::fromPreset(DateRangePreset::Next14Days);
    }

    public static function next30Days(): static
    {
        return static::fromPreset(DateRangePreset::Next30Days);
    }

    public static function next3Months(): static
    {
        return static::fromPreset(DateRangePreset::Next3Months);
    }

    public static function next6Months(): static
    {
        return static::fromPreset(DateRangePreset::Next6Months);
    }

    public static function allTime($start): static
    {
        $instance = new static((static::parseDate($start) ?? Carbon::now())->startOfDay(), Carbon::now()->endOfDay());

        $instance->preset = DateRangePreset::AllTime;

        return $instance;
    }
}
