<?php

namespace TALLKit\Tests\Unit\Livewire;

use Carbon\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use TALLKit\Livewire\DateRange;
use TALLKit\Livewire\DateRangePreset;
use TALLKit\Tests\TestCase;

class DateRangeTest extends TestCase
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

    public function test_between_normalizes_to_whole_days(): void
    {
        $range = DateRange::between('2024-01-05 13:00', '2024-01-10 08:00');

        $this->assertSame('2024-01-05 00:00:00', $range->start()->format('Y-m-d H:i:s'));
        $this->assertSame('2024-01-10 23:59:59', $range->end()->format('Y-m-d H:i:s'));
        $this->assertTrue($range->hasStart());
        $this->assertTrue($range->hasEnd());
        $this->assertFalse($range->hasPreset());
        $this->assertNull($range->preset());
        $this->assertFalse($range->isAllTime());
    }

    public function test_between_swaps_reversed_dates(): void
    {
        $range = DateRange::between('2024-01-10', '2024-01-05');

        $this->assertSame('2024-01-05 00:00:00', $range->start()->format('Y-m-d H:i:s'));
        $this->assertSame('2024-01-10 23:59:59', $range->end()->format('Y-m-d H:i:s'));
    }

    public function test_between_without_end(): void
    {
        $range = DateRange::between('2024-01-05 13:00');

        $this->assertSame('2024-01-05 00:00:00', $range->start()->format('Y-m-d H:i:s'));
        $this->assertNull($range->end());
        $this->assertFalse($range->hasEnd());
        $this->assertSame(
            ['2024-01-05 00:00:00', '2024-01-05 23:59:59'],
            array_map(fn ($date) => $date->format('Y-m-d H:i:s'), $range->bounds()),
        );
    }

    public function test_bounds_with_end(): void
    {
        $range = DateRange::between('2024-01-05', '2024-01-06');

        $this->assertSame(
            ['2024-01-05 00:00:00', '2024-01-06 23:59:59'],
            array_map(fn ($date) => $date->format('Y-m-d H:i:s'), $range->bounds()),
        );
    }

    public function test_between_without_start_returns_null(): void
    {
        $this->assertNull(DateRange::between(null, '2024-01-05'));
        $this->assertNull(DateRange::between('', '2024-01-05'));
        $this->assertNull(DateRange::between('not a date'));
    }

    public function test_parse_date(): void
    {
        $this->assertNull(DateRange::parseDate(null));
        $this->assertNull(DateRange::parseDate('  '));
        $this->assertNull(DateRange::parseDate('not a date'));
        $this->assertInstanceOf(Carbon::class, DateRange::parseDate('2024-01-05'));
        $this->assertSame('2024-01-05', DateRange::parseDate(Carbon::parse('2024-01-05'))->format('Y-m-d'));
    }

    public function test_custom(): void
    {
        $range = DateRange::custom('2024-01-05', '2024-01-10');

        $this->assertSame(DateRangePreset::Custom, $range->preset());
        $this->assertTrue($range->hasPreset());
        $this->assertSame('2024-01-05', $range->start()->format('Y-m-d'));
        $this->assertSame('2024-01-10', $range->end()->format('Y-m-d'));
        $this->assertNull(DateRange::custom(null));
    }

    public function test_from_preset_rejects_all_time(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('::allTime($start)');

        DateRange::fromPreset(DateRangePreset::AllTime);
    }

    public function test_from_preset_rejects_custom(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('::custom($start, $end)');

        DateRange::fromPreset(DateRangePreset::Custom);
    }

    public static function constructors(): array
    {
        return array_map(fn (DateRangePreset $preset) => [$preset->value, $preset], array_filter(
            DateRangePreset::cases(),
            fn (DateRangePreset $preset) => ! in_array($preset, [DateRangePreset::AllTime, DateRangePreset::Custom], true),
        ));
    }

    #[DataProvider('constructors')]
    public function test_named_constructors(string $method, DateRangePreset $preset): void
    {
        $range = DateRange::{$method}();
        [$start, $end] = $preset->dates();

        $this->assertInstanceOf(DateRange::class, $range);
        $this->assertSame($preset, $range->preset());
        $this->assertEquals($start, $range->start());
        $this->assertEquals($end, $range->end());
    }

    public function test_all_time(): void
    {
        $range = DateRange::allTime('2020-03-04 15:00');

        $this->assertTrue($range->isAllTime());
        $this->assertSame('2020-03-04 00:00:00', $range->start()->format('Y-m-d H:i:s'));
        $this->assertSame('2024-05-15 23:59:59', $range->end()->format('Y-m-d H:i:s'));
    }

    public function test_all_time_without_a_valid_start_starts_today(): void
    {
        $range = DateRange::allTime('nope');

        $this->assertSame('2024-05-15 00:00:00', $range->start()->format('Y-m-d H:i:s'));
    }
}
