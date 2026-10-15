<?php

namespace TALLKit\Tests\Unit\Livewire;

use Carbon\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use TALLKit\Livewire\DateRangePreset;
use TALLKit\Tests\TestCase;

class DateRangePresetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2024-05-15 10:30:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public static function presets(): array
    {
        return [
            'today' => [DateRangePreset::Today, '2024-05-15 00:00:00', '2024-05-15 23:59:59'],
            'yesterday' => [DateRangePreset::Yesterday, '2024-05-14 00:00:00', '2024-05-14 23:59:59'],
            'this week' => [DateRangePreset::ThisWeek, '2024-05-13 00:00:00', '2024-05-19 23:59:59'],
            'last week' => [DateRangePreset::LastWeek, '2024-05-06 00:00:00', '2024-05-12 23:59:59'],
            'last 7 days' => [DateRangePreset::Last7Days, '2024-05-09 00:00:00', '2024-05-15 23:59:59'],
            'this month' => [DateRangePreset::ThisMonth, '2024-05-01 00:00:00', '2024-05-31 23:59:59'],
            'last month' => [DateRangePreset::LastMonth, '2024-04-01 00:00:00', '2024-04-30 23:59:59'],
            'this quarter' => [DateRangePreset::ThisQuarter, '2024-04-01 00:00:00', '2024-06-30 23:59:59'],
            'last quarter' => [DateRangePreset::LastQuarter, '2024-01-01 00:00:00', '2024-03-31 23:59:59'],
            'this year' => [DateRangePreset::ThisYear, '2024-01-01 00:00:00', '2024-12-31 23:59:59'],
            'last year' => [DateRangePreset::LastYear, '2023-01-01 00:00:00', '2023-12-31 23:59:59'],
            'last 14 days' => [DateRangePreset::Last14Days, '2024-05-02 00:00:00', '2024-05-15 23:59:59'],
            'last 30 days' => [DateRangePreset::Last30Days, '2024-04-16 00:00:00', '2024-05-15 23:59:59'],
            'last 3 months' => [DateRangePreset::Last3Months, '2024-02-16 00:00:00', '2024-05-15 23:59:59'],
            'last 6 months' => [DateRangePreset::Last6Months, '2023-11-16 00:00:00', '2024-05-15 23:59:59'],
            'year to date' => [DateRangePreset::YearToDate, '2024-01-01 00:00:00', '2024-05-15 23:59:59'],
            'tomorrow' => [DateRangePreset::Tomorrow, '2024-05-16 00:00:00', '2024-05-16 23:59:59'],
            'next week' => [DateRangePreset::NextWeek, '2024-05-20 00:00:00', '2024-05-26 23:59:59'],
            'next 7 days' => [DateRangePreset::Next7Days, '2024-05-15 00:00:00', '2024-05-21 23:59:59'],
            'next month' => [DateRangePreset::NextMonth, '2024-06-01 00:00:00', '2024-06-30 23:59:59'],
            'next quarter' => [DateRangePreset::NextQuarter, '2024-07-01 00:00:00', '2024-09-30 23:59:59'],
            'next year' => [DateRangePreset::NextYear, '2025-01-01 00:00:00', '2025-12-31 23:59:59'],
            'next 14 days' => [DateRangePreset::Next14Days, '2024-05-15 00:00:00', '2024-05-28 23:59:59'],
            'next 30 days' => [DateRangePreset::Next30Days, '2024-05-15 00:00:00', '2024-06-13 23:59:59'],
            'next 3 months' => [DateRangePreset::Next3Months, '2024-05-15 00:00:00', '2024-08-14 23:59:59'],
            'next 6 months' => [DateRangePreset::Next6Months, '2024-05-15 00:00:00', '2024-11-14 23:59:59'],
        ];
    }

    #[DataProvider('presets')]
    public function test_dates(DateRangePreset $preset, string $start, string $end): void
    {
        [$from, $to] = $preset->dates();

        $this->assertSame($start, $from->format('Y-m-d H:i:s'));
        $this->assertSame($end, $to->format('Y-m-d H:i:s'));
    }

    public function test_dates_do_not_mutate_now(): void
    {
        DateRangePreset::LastYear->dates();

        $this->assertSame('2024-05-15 10:30:00', Carbon::now()->format('Y-m-d H:i:s'));
    }

    public function test_week_follows_the_locale_first_day(): void
    {
        $this->app->setLocale('en_US');

        [$start, $end] = DateRangePreset::ThisWeek->dates();

        $this->assertSame('2024-05-12 00:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertSame('2024-05-18 23:59:59', $end->format('Y-m-d H:i:s'));
    }

    public function test_next_months_keeps_the_month_end_when_the_day_overflows(): void
    {
        Carbon::setTestNow('2024-11-30 08:00:00');

        [, $end] = DateRangePreset::Next3Months->dates();

        $this->assertSame('2025-02-28 23:59:59', $end->format('Y-m-d H:i:s'));
    }

    public function test_all_time_uses_the_given_start(): void
    {
        [$start, $end] = DateRangePreset::AllTime->dates(Carbon::parse('2020-03-04 15:00:00'));

        $this->assertSame('2020-03-04 00:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertSame('2024-05-15 23:59:59', $end->format('Y-m-d H:i:s'));
    }

    public function test_all_time_without_start(): void
    {
        [$start, $end] = DateRangePreset::AllTime->dates();

        $this->assertNull($start);
        $this->assertSame('2024-05-15 23:59:59', $end->format('Y-m-d H:i:s'));
    }

    public function test_custom_has_no_dates(): void
    {
        $this->expectException(\LogicException::class);

        DateRangePreset::Custom->dates();
    }

    public function test_labels(): void
    {
        $labels = array_map(fn (DateRangePreset $preset) => $preset->label(), DateRangePreset::cases());

        $this->assertCount(count(DateRangePreset::cases()), array_unique($labels));
        $this->assertSame('Today', DateRangePreset::Today->label());
        $this->assertSame('Year to Date', DateRangePreset::YearToDate->label());
        $this->assertSame('Next 6 Months', DateRangePreset::Next6Months->label());
        $this->assertSame('All Time', DateRangePreset::AllTime->label());
        $this->assertSame('Custom', DateRangePreset::Custom->label());
    }

    public function test_labels_are_translated(): void
    {
        $this->app->setLocale('pt_BR');

        $this->assertSame('Hoje', DateRangePreset::Today->label());
        $this->assertSame('Ano até hoje', DateRangePreset::YearToDate->label());
    }
}
