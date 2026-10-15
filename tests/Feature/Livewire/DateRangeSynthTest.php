<?php

namespace TALLKit\Tests\Feature\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleComponents\ComponentContext;
use TALLKit\Livewire\DateRange;
use TALLKit\Livewire\DateRangePreset;
use TALLKit\Livewire\DateRangeSynth;
use TALLKit\Tests\TestCase;

class DateRangeSynthTest extends TestCase
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

    protected function synth(?object $component = null): DateRangeSynth
    {
        return new DateRangeSynth(new ComponentContext($component ?? new class extends Component {}), 'range');
    }

    protected function dates(?DateRange $range): ?array
    {
        return $range ? [$range->start()?->format('Y-m-d'), $range->end()?->format('Y-m-d'), $range->preset()?->value] : null;
    }

    public function test_match(): void
    {
        $this->assertTrue(DateRangeSynth::match(DateRange::today()));
        $this->assertFalse(DateRangeSynth::match(Carbon::now()));
        $this->assertTrue(DateRangeSynth::matchByType(DateRange::class));
        $this->assertFalse(DateRangeSynth::matchByType(Carbon::class));
    }

    public function test_dehydrate(): void
    {
        $dehydrate = fn ($range) => $this->synth()->dehydrate($range, fn () => null);

        $this->assertSame([['start' => '2024-01-05', 'end' => '2024-01-10'], []], $dehydrate(DateRange::between('2024-01-05', '2024-01-10')));
        $this->assertSame([['start' => '2024-01-05', 'end' => null], []], $dehydrate(DateRange::between('2024-01-05')));
        $this->assertSame([['start' => '2024-05-15', 'end' => '2024-05-15', 'preset' => 'today'], []], $dehydrate(DateRange::today()));
    }

    public function test_unwrap_for_validation(): void
    {
        $this->assertSame(
            ['start' => '2024-01-05', 'end' => '2024-01-10', 'preset' => 'custom'],
            DateRangeSynth::unwrapForValidation(DateRange::custom('2024-01-05', '2024-01-10')),
        );
    }

    public function test_hydrate(): void
    {
        $hydrate = fn ($value) => $this->dates($this->synth()->hydrate($value, []));

        $this->assertSame(['2024-01-05', '2024-01-10', null], $hydrate(['start' => '2024-01-05', 'end' => '2024-01-10']));
        $this->assertSame(['2024-01-05', null, null], $hydrate(['start' => '2024-01-05']));
        $this->assertSame(['2024-01-05', '2024-01-10', 'custom'], $hydrate(['start' => '2024-01-05', 'end' => '2024-01-10', 'preset' => 'custom']));
        $this->assertSame(['2024-05-13', '2024-05-19', 'thisWeek'], $hydrate(['start' => '1999-01-01', 'preset' => 'thisWeek']));
        $this->assertSame(['2020-01-01', '2024-05-15', 'allTime'], $hydrate(['start' => '2020-01-01', 'preset' => 'allTime']));
        $this->assertNull($hydrate(['preset' => 'allTime']));
        $this->assertSame(['2024-01-05', '2024-01-10', null], $hydrate(['start' => '2024-01-05', 'end' => '2024-01-10', 'preset' => 'unknown']));
        $this->assertNull($hydrate(['start' => 123456, 'end' => '2024-01-10']));
        $this->assertSame(['2024-01-05', '2024-01-10', null], $hydrate(['start' => '2024-01-05', 'end' => '2024-01-10', 'preset' => ['today']]));
        $this->assertNull($hydrate(123));
        $this->assertNull($hydrate([]));
    }

    public function test_hydrate_from_string(): void
    {
        $this->assertSame(['2024-01-05', '2024-01-10', null], $this->dates($this->synth()->hydrate('2024-01-05/2024-01-10', [])));
        $this->assertSame(['2024-01-05', null, null], $this->dates(DateRangeSynth::hydrateFromType(DateRange::class, '2024-01-05')));
    }

    public function test_set_start_and_end(): void
    {
        $synth = $this->synth();
        $target = DateRange::custom('2024-01-05', '2024-01-10');

        $synth->set($target, 'start', '2024-01-07');
        $this->assertSame(['2024-01-07', '2024-01-10', null], $this->dates($target));

        $synth->set($target, 'end', '2024-01-20');
        $this->assertSame(['2024-01-07', '2024-01-20', null], $this->dates($target));

        $synth->set($target, 'end', '');
        $this->assertSame(['2024-01-07', null, null], $this->dates($target));
    }

    public function test_set_ignores_unreadable_values(): void
    {
        $synth = $this->synth();
        $target = $original = DateRange::between('2024-01-05', '2024-01-10');

        $synth->set($target, 'start', 'not a date');
        $synth->set($target, 'start', 20240101);
        $synth->set($target, 'end', ['2024-01-01']);
        $synth->set($target, 'other', '2024-01-01');

        $this->assertSame($original, $target);
    }

    public function test_set_preset(): void
    {
        $synth = $this->synth();
        $target = DateRange::between('2024-01-05', '2024-01-10');

        $synth->set($target, 'preset', 'unknown');
        $this->assertSame(['2024-01-05', '2024-01-10', null], $this->dates($target));

        $synth->set($target, 'preset', 123);
        $this->assertSame(['2024-01-05', '2024-01-10', null], $this->dates($target));

        $synth->set($target, 'preset', 'custom');
        $this->assertSame(['2024-01-05', '2024-01-10', 'custom'], $this->dates($target));

        $synth->set($target, 'preset', 'yesterday');
        $this->assertSame(['2024-05-14', '2024-05-14', 'yesterday'], $this->dates($target));

        $synth->set($target, 'preset', 'allTime');
        $this->assertSame(['2024-05-14', '2024-05-15', 'allTime'], $this->dates($target));
    }

    public function test_set_all_time_preset_uses_the_component_start(): void
    {
        $component = new class extends Component
        {
            public array $properties = [];

            public function dateRangeAllTimeStart($property)
            {
                $this->properties[] = $property;

                return '2019-06-01';
            }
        };

        $target = DateRange::between('2024-01-05', '2024-01-10');

        $this->synth($component)->set($target, 'preset', 'allTime', 'range');

        $this->assertSame(['2019-06-01', '2024-05-15', 'allTime'], $this->dates($target));
        $this->assertSame(['range'], $component->properties);
    }

    public function test_unset(): void
    {
        $synth = $this->synth();

        $target = DateRange::custom('2024-01-05', '2024-01-10');
        $synth->unset($target, 'preset');
        $this->assertSame(['2024-01-05', '2024-01-10', null], $this->dates($target));

        $synth->unset($target, 'other');
        $this->assertSame(['2024-01-05', '2024-01-10', null], $this->dates($target));

        $synth->unset($target, 'end');
        $this->assertSame(['2024-01-05', null, null], $this->dates($target));

        $synth->unset($target, 'start');
        $this->assertNull($target);
    }

    public function test_livewire_round_trip(): void
    {
        $component = Livewire::test(new class extends Component
        {
            public ?DateRange $range = null;

            public function mount()
            {
                $this->range = DateRange::custom('2024-01-05', '2024-01-10');
            }

            public function validateRange()
            {
                $this->validate(['range.start' => 'required|date', 'range.preset' => 'in:custom']);
            }

            public function render()
            {
                return '<div></div>';
            }
        });

        $this->assertSame(
            [['start' => '2024-01-05', 'end' => '2024-01-10', 'preset' => 'custom'], ['s' => 'tkdr']],
            $component->snapshot['data']['range'],
        );

        $component->call('validateRange')->assertHasNoErrors();

        $component->set('range.start', '2024-01-08');
        $this->assertSame(['2024-01-08', '2024-01-10', null], $this->dates($component->get('range')));

        $component->set('range.preset', 'today');
        $this->assertSame(['2024-05-15', '2024-05-15', 'today'], $this->dates($component->get('range')));

        $component->call('validateRange')->assertHasErrors(['range.preset']);

        $component->set('range', '2024-02-01/2024-02-03');
        $this->assertSame(['2024-02-01', '2024-02-03', null], $this->dates($component->get('range')));
    }

    public function test_livewire_hydrates_a_null_property_from_its_type(): void
    {
        $component = Livewire::test(new class extends Component
        {
            public ?DateRange $range = null;

            public function render()
            {
                return '<div></div>';
            }
        });

        $component->set('range', '2024-02-01/2024-02-03');

        $this->assertInstanceOf(DateRange::class, $component->get('range'));
        $this->assertSame(['2024-02-01', '2024-02-03', null], $this->dates($component->get('range')));
    }

    public function test_preset_value_round_trips(): void
    {
        $this->assertSame(DateRangePreset::Today, $this->synth()->hydrate(['preset' => 'today'], [])->preset());
    }
}
