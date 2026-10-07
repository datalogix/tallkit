@props([
    'value' => null,
    'name' => null,
    'id' => null,
    'size' => null,
    'color' => null,
    'multiple' => null,
    'range' => null,
    'months' => null,
    'min' => null,
    'max' => null,
    'unavailable' => null,
    'minRange' => null,
    'maxRange' => null,
    'static' => null,
    'navigation' => null,
    'today' => null,
    'selectableHeader' => null,
    'fixedWeeks' => null,
    'startDay' => null,
    'openTo' => null,
    'weekNumbers' => null,
    'locale' => null,
    'standalone' => null,
])
@php

$locale = TALLKit::resolveLocale($locale);
$startDay ??= TALLKit::localeFirstDay($locale);

[$name, $fieldName, , , $invalid, $wireModel, $id] = TALLKit::fieldContext(attributes: $attributes, label: false, id: $id);
$value = TALLKit::fieldOldValue($fieldName, $value);

$value = TALLKit::fieldDateValue($value);
$min = TALLKit::fieldDateValue($min);
$max = TALLKit::fieldDateValue($max);
$unavailable = TALLKit::fieldDateValue($unavailable);
$openTo = TALLKit::fieldDateValue($openTo);

if (is_string($value) && $range) {
    [$rangeStart, $rangeEnd] = array_pad(explode('/', $value, 2), 2, null);
    $value = ['start' => $rangeStart, 'end' => $rangeEnd];
} elseif (is_string($value) && $multiple) {
    $value = array_values(array_filter(explode(',', $value)));
}

$monthsCount = max(1, (int) ($months ?? 1));
$isStatic = (bool) $static;
$canNavigate = ! $isStatic && $navigation !== false;

$initialValueString = match (true) {
    $range => is_array($value) && ($value['start'] ?? null)
        ? (($value['end'] ?? null) ? $value['start'].'/'.$value['end'] : $value['start'])
        : '',
    (bool) $multiple => is_array($value) ? implode(',', $value) : ($value ?? ''),
    default => is_array($value) ? '' : ($value ?? ''),
};

@endphp
<div
    @if ($standalone !== false)
        wire:ignore
        x-data="calendar(@js([
            'value' => $value,
            'multiple' => (bool) $multiple,
            'range' => (bool) $range,
            'months' => $monthsCount,
            'min' => $min,
            'max' => $max,
            'unavailable' => $unavailable,
            'minRange' => $minRange,
            'maxRange' => $maxRange,
            'static' => $isStatic,
            'navigation' => $navigation !== false,
            'today' => (bool) $today,
            'selectableHeader' => (bool) $selectableHeader,
            'fixedWeeks' => (bool) $fixedWeeks,
            'startDay' => $startDay,
            'openTo' => $openTo,
            'weekNumbers' => (bool) $weekNumbers,
            'locale' => $locale,
        ]))"
    @endif
    x-cloak
    {{
        $attributes
            ->whereDoesntStartWith([
                'hidden:',
                'header:', 'nav-prev:', 'nav-next:', 'today:',
                'months:', 'month:', 'month-label:', 'month-select:', 'year-select:',
                'weekdays:', 'weekday:', 'week:', 'week-number:', 'day:',
            ])
            ->dataKey('calendar')
            ->merge(['data-invalid' => $invalid ? true : null])
            ->classes([
                'inline-flex flex-col',
                TALLKit::gap(size: $size),
                'tk-control-standalone' => $standalone !== false,
                TALLKit::padding(size: $size) => $standalone !== false,
                TALLKit::roundedSize(size: $size) => $standalone !== false,
            ])
    }}
>
    @if ($standalone !== false)
        <input
            type="hidden"
            {{
                $attributes->prefixed('hidden:')
                    ->dataKey('calendar-field')
                    ->merge([
                        'name' => $name,
                        'value' => in_livewire() ? null : $initialValueString,
                        'wire:model' => $wireModel,
                    ])
            }}
        />
    @endif

    <div
        {{
            $attributes->prefixed('header:')
                ->classes('flex items-center justify-between gap-2')
        }}
    >
        <div class="flex items-center gap-2">
            @if ($selectableHeader && $canNavigate && $monthsCount === 1)
                <tk:select
                    :attributes="$attributes->prefixed('month-select:')->classes('border-none min-w-32')->merge(['aria-label' => __('Month')])"
                    :size="TALLKit::adjustSize(size: $size, move: -2)"
                    :label="false"
                    :placeholder="false"
                    x-init="$nextTick(() => $el.value = currentMonthIndex())"
                    x-bind:value="currentMonthIndex()"
                    @change="setCurrentMonthIndex($event.target.value)"
                >
                    <template x-for="option in monthOptions()" :key="option.value">
                        <option :value="option.value" x-text="option.label"></option>
                    </template>
                </tk:select>

                <tk:select
                    :attributes="$attributes->prefixed('year-select:')->classes('border-none')->merge(['aria-label' => __('Year')])"
                    :size="TALLKit::adjustSize(size: $size, move: -2)"
                    :label="false"
                    :placeholder="false"
                    x-init="$nextTick(() => $el.value = currentYear())"
                    x-bind:value="currentYear()"
                    @change="setCurrentYear($event.target.value)"
                >
                    <template x-for="year in yearOptions()" :key="year">
                        <option :value="year" x-text="year"></option>
                    </template>
                </tk:select>
            @elseif ($monthsCount === 1)
                <div
                    {{ $attributes->prefixed('month-label:')->classes(TALLKit::fontSize(size: $size, weight: true), TALLKit::textNeutral(variant: 'strong')) }}
                    x-text="monthLabel(0)"
                ></div>
            @endif
        </div>

        <div class="flex items-center gap-px">
            @if ($today && ! $isStatic)
                <tk:button
                    :attributes="$attributes->prefixed('today:')->classes(TALLKit::roundedSize(size: $size, mode: 'large'))->merge(['tooltip' => 'Today'])"
                    :size="TALLKit::adjustSize(size: $size)"
                    variant="subtle"
                    icon="calendar-today"
                    @click="goToToday()"
                />
            @endif

            @if ($canNavigate)
                <tk:button
                    :attributes="$attributes->prefixed('nav-prev:')->classes(TALLKit::roundedSize(size: $size, mode: 'large'))->merge(['tooltip' => 'Previous month'])"
                    :size="TALLKit::adjustSize(size: $size)"
                    variant="subtle"
                    icon="chevron-left"
                    icon:class="rtl:-scale-x-100"
                    @click="prevMonth()"
                />

                <tk:button
                    :attributes="$attributes->prefixed('nav-next:')->classes(TALLKit::roundedSize(size: $size, mode: 'large'))->merge(['tooltip' => 'Next month'])"
                    :size="TALLKit::adjustSize(size: $size)"
                    variant="subtle"
                    icon="chevron-right"
                    icon:class="rtl:-scale-x-100"
                    @click="nextMonth()"
                />
            @endif
        </div>
    </div>

    <div
        {{
            $attributes->prefixed('months:')
                ->classes(
                    'flex flex-col sm:flex-row',
                    TALLKit::gap(size: $size)
                )
        }}
    >
        @for ($m = 0; $m < $monthsCount; $m++)
            <div
                role="grid"
                x-bind:aria-label="monthLabel({{ $m }})"
                {{
                    $attributes->prefixed('month:')
                        ->classes(
                            'flex-1 flex flex-col',
                            TALLKit::gap(size: $size, mode: 'small')
                        )
                        ->merge(['aria-label' => __('Calendar')])
                }}
            >
                @if ($monthsCount > 1)
                    <div
                        {{
                            $attributes->prefixed('month-label:')
                                ->classes(
                                    'text-center',
                                    TALLKit::fontSize(size: $size, weight: true),
                                    TALLKit::textNeutral(variant: 'strong'),
                                )
                        }}
                        x-text="monthLabel({{ $m }})"
                    ></div>
                @endif

                <div
                    role="row"
                    {{
                        $attributes->prefixed('weekdays:')
                            ->classes(
                                'grid text-center',
                                TALLKit::textNeutral(variant: 'muted'),
                                $weekNumbers ? 'grid-cols-8' : 'grid-cols-7',
                                TALLKit::fontSize(size: $size, mode: 'small'),
                            )
                    }}
                >
                    @if ($weekNumbers)
                        <div role="columnheader"><span class="sr-only">{{ __('Week') }}</span></div>
                    @endif

                    <template x-for="(label, index) in weekdayLabels()" :key="index">
                        <div
                            role="columnheader"
                            {{
                                $attributes->prefixed('weekday:')
                                    ->classes('py-1')
                            }}
                            x-text="label"
                        ></div>
                    </template>
                </div>

                <template x-for="week in weeksFor({{ $m }})" :key="week.key">
                    <div
                        role="row"
                        {{
                            $attributes->prefixed('week:')
                                ->classes('grid', $weekNumbers ? 'grid-cols-8' : 'grid-cols-7')
                        }}
                    >
                        @if ($weekNumbers)
                            <div
                                role="rowheader"
                                {{
                                    $attributes->prefixed('week-number:')
                                        ->classes([
                                            'flex items-center justify-center',
                                            TALLKit::textNeutral(variant: 'muted'),
                                            TALLKit::fontSize(size: $size, mode: 'smallest'),
                                        ])
                                }}
                                x-text="week.weekNumber"
                            ></div>
                        @endif

                        <template x-for="day in week.days" :key="day.iso">
                            <tk:button
                                :attributes="$attributes->prefixed('day:')->classes([
                                    '
                                        relative z-10 tabular-nums

                                        [&[data-today]]:font-semibold
                                        [&[data-today]]:after:content-[\'\'] [&[data-today]]:after:absolute
                                        [&[data-today]]:after:bottom-0.5 [&[data-today]]:after:left-1/2
                                        [&[data-today]]:after:-translate-x-1/2 [&[data-today]]:after:size-1
                                        [&[data-today]]:after:rounded-full [&[data-today]]:after:bg-current

                                        [&[data-unavailable]]:disabled:opacity-40
                                        [&[data-unavailable]]:disabled:line-through

                                        [&[data-outside-month]]:opacity-75
                                        [&[data-active][data-outside-month]]:opacity-100

                                        [&[data-in-range]]:rounded-none

                                        [&[data-range-start]]:rounded-e-none
                                        [&[data-range-end]]:rounded-s-none
                                        [&[data-range-start][data-range-end]]:rounded
                                    ',
                                    'tk-color-'.$color.'
                                        [&[data-active]]:bg-(--tk-fill) [&[data-active]]:text-(--tk-on-fill)
                                        [&[data-in-range]]:bg-(--tk-faint) [&[data-in-range]]:enabled:hover:bg-(--tk-soft)
                                    ' => TALLKit::isColor($color),
                                    '
                                        [&[data-active]]:bg-[var(--color-accent)]
                                        [&[data-active]]:text-[var(--color-accent-foreground)]
                                        [&[data-in-range]]:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_90%)]
                                        [&[data-in-range]]:enabled:hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_85%)]
                                    ' => ! TALLKit::isColor($color),
                                    'disabled:opacity-100' => $isStatic,
                                ])"
                                type="button"
                                variant="ghost"
                                square
                                :size="$size"
                                role="gridcell"
                                ::aria-label="dayAriaLabel(day.iso)"
                                ::aria-selected="isSelected(day.iso)"
                                ::aria-current="isToday(day.iso) ? 'date' : false"
                                ::data-active="isSelected(day.iso)"
                                ::data-today="isToday(day.iso)"
                                ::data-outside-month="!day.inMonth"
                                ::data-iso="day.iso"
                                ::data-in-range="isInRange(day.iso)"
                                ::data-range-start="isRangeStart(day.iso)"
                                ::data-range-end="isRangeEnd(day.iso)"
                                ::tabindex="day.inMonth && tabbableIso() === day.iso ? 0 : -1"
                                ::disabled="isDayDisabled(day.iso)"
                                ::data-unavailable="isUnavailable(day.iso)"
                                @click="selectDate(day.iso)"
                                @mouseenter="previewRange(day.iso)"
                                @keydown="onCellKeydown($event, day.iso)"
                            >
                                <span x-text="day.label"></span>
                            </tk:button>
                        </template>
                    </div>
                </template>
            </div>
        @endfor
    </div>
</div>
