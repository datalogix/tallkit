<?php

namespace TALLKit\Livewire;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use TALLKit\Facades\TALLKit;

enum DateRangePreset: string
{
    case Today = 'today';
    case Yesterday = 'yesterday';
    case ThisWeek = 'thisWeek';
    case LastWeek = 'lastWeek';
    case Last7Days = 'last7Days';
    case ThisMonth = 'thisMonth';
    case LastMonth = 'lastMonth';
    case ThisQuarter = 'thisQuarter';
    case LastQuarter = 'lastQuarter';
    case ThisYear = 'thisYear';
    case LastYear = 'lastYear';
    case Last14Days = 'last14Days';
    case Last30Days = 'last30Days';
    case Last3Months = 'last3Months';
    case Last6Months = 'last6Months';
    case YearToDate = 'yearToDate';
    case Tomorrow = 'tomorrow';
    case NextWeek = 'nextWeek';
    case Next7Days = 'next7Days';
    case NextMonth = 'nextMonth';
    case NextQuarter = 'nextQuarter';
    case NextYear = 'nextYear';
    case Next14Days = 'next14Days';
    case Next30Days = 'next30Days';
    case Next3Months = 'next3Months';
    case Next6Months = 'next6Months';
    case AllTime = 'allTime';
    case Custom = 'custom';

    // The day moves before the months to avoid overflow (Feb 31 is Mar 3).
    public function dates(?CarbonInterface $start = null)
    {
        $now = Carbon::now();

        return match ($this) {
            self::Today => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            self::Yesterday => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            self::ThisWeek => self::week($now),
            self::LastWeek => self::week($now->copy()->subWeek()),
            self::Last7Days => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            self::ThisMonth => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            self::LastMonth => [$now->copy()->startOfMonth()->subMonth(), $now->copy()->startOfMonth()->subMonth()->endOfMonth()],
            self::ThisQuarter => [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()],
            self::LastQuarter => [$now->copy()->startOfQuarter()->subQuarter(), $now->copy()->startOfQuarter()->subQuarter()->endOfQuarter()],
            self::ThisYear => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            self::LastYear => [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()],
            self::Last14Days => [$now->copy()->subDays(13)->startOfDay(), $now->copy()->endOfDay()],
            self::Last30Days => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
            self::Last3Months => [$now->copy()->addDay()->subMonthsNoOverflow(3)->startOfDay(), $now->copy()->endOfDay()],
            self::Last6Months => [$now->copy()->addDay()->subMonthsNoOverflow(6)->startOfDay(), $now->copy()->endOfDay()],
            self::YearToDate => [$now->copy()->startOfYear(), $now->copy()->endOfDay()],
            self::Tomorrow => [$now->copy()->addDay()->startOfDay(), $now->copy()->addDay()->endOfDay()],
            self::NextWeek => self::week($now->copy()->addWeek()),
            self::Next7Days => [$now->copy()->startOfDay(), $now->copy()->addDays(6)->endOfDay()],
            self::NextMonth => [$now->copy()->startOfMonth()->addMonthNoOverflow(), $now->copy()->startOfMonth()->addMonthNoOverflow()->endOfMonth()],
            self::NextQuarter => [$now->copy()->startOfQuarter()->addQuarter(), $now->copy()->startOfQuarter()->addQuarter()->endOfQuarter()],
            self::NextYear => [$now->copy()->addYear()->startOfYear(), $now->copy()->addYear()->endOfYear()],
            self::Next14Days => [$now->copy()->startOfDay(), $now->copy()->addDays(13)->endOfDay()],
            self::Next30Days => [$now->copy()->startOfDay(), $now->copy()->addDays(29)->endOfDay()],
            self::Next3Months => [$now->copy()->startOfDay(), $now->copy()->subDay()->addMonthsNoOverflow(3)->endOfDay()],
            self::Next6Months => [$now->copy()->startOfDay(), $now->copy()->subDay()->addMonthsNoOverflow(6)->endOfDay()],
            self::AllTime => [$start?->copy()->startOfDay(), $now->copy()->endOfDay()],
            self::Custom => throw new \LogicException('A custom date range has no dates of its own.'),
        };
    }

    protected static function week(Carbon $date): array
    {
        $weekStart = TALLKit::localeFirstDay();

        return [$date->copy()->startOfWeek($weekStart), $date->copy()->endOfWeek(($weekStart + 6) % 7)];
    }

    public function label()
    {
        return match ($this) {
            self::Today => __('Today'),
            self::Yesterday => __('Yesterday'),
            self::ThisWeek => __('This Week'),
            self::LastWeek => __('Last Week'),
            self::Last7Days => __('Last 7 Days'),
            self::ThisMonth => __('This Month'),
            self::LastMonth => __('Last Month'),
            self::ThisQuarter => __('This Quarter'),
            self::LastQuarter => __('Last Quarter'),
            self::ThisYear => __('This Year'),
            self::LastYear => __('Last Year'),
            self::Last14Days => __('Last 14 Days'),
            self::Last30Days => __('Last 30 Days'),
            self::Last3Months => __('Last 3 Months'),
            self::Last6Months => __('Last 6 Months'),
            self::YearToDate => __('Year to Date'),
            self::Tomorrow => __('Tomorrow'),
            self::NextWeek => __('Next Week'),
            self::Next7Days => __('Next 7 Days'),
            self::NextMonth => __('Next Month'),
            self::NextQuarter => __('Next Quarter'),
            self::NextYear => __('Next Year'),
            self::Next14Days => __('Next 14 Days'),
            self::Next30Days => __('Next 30 Days'),
            self::Next3Months => __('Next 3 Months'),
            self::Next6Months => __('Next 6 Months'),
            self::AllTime => __('All Time'),
            self::Custom => __('Custom'),
        };
    }
}
