@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'multiple' => null,
    'range' => null,
    'trigger' => null,
    'months' => null,
    'min' => null,
    'max' => null,
    'unavailable' => null,
    'minRange' => null,
    'maxRange' => null,
    'today' => null,
    'selectableHeader' => null,
    'fixedWeeks' => null,
    'startDay' => null,
    'openTo' => null,
    'forceOpenTo' => null,
    'weekNumbers' => null,
    'locale' => null,
    'clearable' => null,
    'format' => null,
    'inputs' => null,
    'confirm' => null,
    'presets' => null,
])
@php

$locale = TALLKit::resolveLocale($locale);
$startDay ??= TALLKit::localeFirstDay($locale);

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::fieldContext(attributes: $attributes, label: $label, id: $id, scope: get_defined_vars());
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

$placeholderText = __(is_string($placeholder) ? $placeholder : 'Select date');
$disabled = TALLKit::isAttributeEnabled($attributes->get('disabled'));
$readonly = TALLKit::isAttributeEnabled($attributes->get('readonly'));
$describedBy = TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError);
$innerSize = TALLKit::adjustSize(size: $size);

$initialCommittedString = match (true) {
    $range => is_array($value) && ($value['start'] ?? null)
        ? (($value['end'] ?? null) ? $value['start'].'/'.$value['end'] : $value['start'])
        : '',
    (bool) $multiple => is_array($value) ? implode(',', $value) : ($value ?? ''),
    default => is_array($value) ? '' : ($value ?? ''),
};

$showInputs = (bool) $inputs && ! $multiple;
$presetLabels = collect(\TALLKit\Livewire\DateRangePreset::cases())
    ->reject(fn ($preset) => in_array($preset, [\TALLKit\Livewire\DateRangePreset::AllTime, \TALLKit\Livewire\DateRangePreset::Custom], true))
    ->mapWithKeys(fn ($preset) => [$preset->value => $preset->label()])
    ->all();
$defaultPresets = 'today yesterday thisWeek last7Days last14Days last30Days thisMonth lastMonth thisYear lastYear';
$presetKeys = $presets && $range
    ? array_values(array_filter(explode(' ', is_string($presets) ? $presets : $defaultPresets), fn ($key) => isset($presetLabels[$key])))
    : [];
$showPresets = count($presetKeys) > 0;

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <div
        wire:ignore
        x-data="datePicker(@js([
            'value' => $value,
            'multiple' => (bool) $multiple,
            'range' => (bool) $range,
            'dateRange' => $range && $wireModel && TALLKit::livewirePropertyIs($wireModel, \TALLKit\Livewire\DateRange::class),
            'trigger' => $trigger,
            'months' => $months,
            'min' => $min,
            'max' => $max,
            'unavailable' => $unavailable,
            'minRange' => $minRange,
            'maxRange' => $maxRange,
            'today' => (bool) $today,
            'selectableHeader' => (bool) $selectableHeader,
            'fixedWeeks' => (bool) $fixedWeeks,
            'startDay' => $startDay,
            'openTo' => $openTo,
            'forceOpenTo' => (bool) $forceOpenTo,
            'weekNumbers' => (bool) $weekNumbers,
            'locale' => $locale,
            'format' => $format,
            'confirm' => (bool) $confirm,
        ]))"
        {{
            $attributes->prefixed('picker:')
                ->classes('flex-1')
        }}
    >
        <tk:field.control
            :$size
            :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
                ->classes(
                    'tk-control-wrapper-expanded',
                    TALLKit::roundedSize(size: $size, mode: 'large'),
                    TALLKit::controlFocusRingNested(color: $color, expanded: true),
                )
                ->merge(['icon' => 'calendar'])
            "
            :icon:size="$innerSize"
            icon-trailing="chevron-down"
            :icon-trailing:size="$innerSize"
        >
            <input
                type="hidden"
                {{
                    $attributes
                        ->dataKey('date-picker')
                        ->merge([
                            'name' => $name,
                            'value' => in_livewire() ? null : $initialCommittedString,
                            'wire:model' => $wireModel,
                        ])
                        ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: [
                            'picker:', 'trigger:', 'placeholder:', 'formatted:', 'popover:', 'layout:', 'presets:', 'preset:', 'calendar:', 'inputs:',
                            'start-date:', 'divider:', 'end-date:', 'single-date:',
                            'footer:', 'clearable:', 'cancel:', 'apply:',
                        ]))
                }}
            />

            @if ($trigger === 'input')
                <input
                    {{
                        $attributes->prefixed('trigger:')
                            ->dataKey('input')
                            ->dataKey('control')
                            ->dataKey('group-target')
                            ->merge([
                                'type' => 'text',
                                'role' => 'combobox',
                                'aria-haspopup' => 'dialog',
                                'autocomplete' => 'off',
                                'id' => $id,
                                'placeholder' => $placeholderText,
                                'aria-describedby' => $describedBy,
                                'aria-invalid' => $invalid ? 'true' : null,
                                'aria-readonly' => $readonly ? 'true' : null,
                                'data-invalid' => $invalid ? true : null,
                                'disabled' => $disabled ?: null,
                                'readonly' => ($multiple || $readonly) ?: null,
                            ])
                            ->classes(
                                'tk-field-control-base w-full',
                                TALLKit::fontSize(size: $size, mode: 'large'),
                                TALLKit::height(size: $size),
                                TALLKit::paddingStart(size: $size, mode: 'large'),
                                TALLKit::paddingEnd(size: $size, mode: 'large'),
                            )
                    }}
                    @if ($multiple)
                        x-bind:value="formatted() ?? ''"
                    @else
                        x-model="typed"
                        x-mask:dynamic="maskPattern()"
                        @focus="typing = true"
                        @keydown.enter.prevent="confirmTyped()"
                        @blur="onFieldBlur($event)"
                    @endif
                />
            @else
                <tk:button
                    :attributes="$attributes->prefixed('trigger:')
                        ->dataKey('control')
                        ->merge([
                            'id' => $id,
                            'aria-labelledby' => trim(($label ? $id.'-label ' : '').$id.'-value'),
                            'aria-describedby' => $describedBy,
                            'aria-invalid' => $invalid ? 'true' : null,
                            'aria-readonly' => $readonly ? 'true' : null,
                            'data-invalid' => $invalid ? true : null,
                        ])
                        ->classes(
                            '
                                [:where(&)]:w-full
                                [:where(&)]:justify-start
                                [:where(&)]:font-normal
                            ',
                            TALLKit::textNeutral(prefix: '[:where(&)]:'),
                            TALLKit::textNeutral(prefix: 'hover:[:where(&)]:'),
                            TALLKit::height(size: $size),
                            TALLKit::paddingInline(size: $size, mode: 'large'),
                            TALLKit::fontSize(size: $size, mode: 'large'),
                        )
                    "
                    variant="none"
                    :$disabled
                    :$size
                >
                    <span
                        x-show="!formatted()"
                        {{
                            $attributes->prefixed('placeholder:')
                                ->classes(TALLKit::textNeutral(variant: 'muted'))
                        }}
                    >{{ $placeholderText }}</span>
                    <span
                        x-show="formatted()"
                        x-text="formatted()"
                        {{
                            $attributes->prefixed('formatted:')
                                ->merge(['id' => $id.'-value'])
                                ->classes('overflow-hidden text-ellipsis')
                        }}
                    ></span>
                </tk:button>
            @endif
        </tk:field.control>

        <tk:popover
            :attributes="$attributes->prefixed('popover:')->classes('p-0 max-h-full')"
            :$size
            animation="none"
            keep-open
        >
            <div
                {{
                    $attributes->prefixed('layout:')
                        ->classes(['flex divide-x divide-zinc-100 dark:divide-white/10' => $showPresets])
                }}
            >
                @if ($showPresets)
                    <div {{ $attributes->prefixed('presets:')->classes('flex flex-col gap-0.5 p-1') }}>
                        @foreach ($presetKeys as $key)
                            <tk:button
                                :attributes="$attributes->prefixed('preset:')->classes('w-full justify-start')"
                                :size="$innerSize"
                                :label="$presetLabels[$key]"
                                ::data-active="isPresetActive('{{ $key }}')"
                                ::disabled="!presetAvailable('{{ $key }}')"
                                type="button"
                                variant="ghost"
                                @click="applyPreset('{{ $key }}')"
                            />
                        @endforeach
                    </div>
                @endif

                <div class="min-w-0 flex-1">
                    <tk:calendar
                        :attributes="$attributes->prefixed('calendar:')->classes(TALLKit::padding(size: $size))"
                        :$size
                        :$color
                        :$months
                        :$today
                        :$selectableHeader
                        :$weekNumbers
                        :static="false"
                        :standalone="false"
                    />

                    @if ($showInputs)
                        <div {{ $attributes->prefixed('inputs:')->classes('flex items-center gap-2 border-t border-zinc-100 p-2 dark:border-white/10') }}>
                            @if ($range)
                                <input
                                    {{
                                        $attributes->prefixed('start-date:')
                                            ->classes(
                                                '
                                                    flex-1
                                                    tk-field-control-base
                                                    tk-control-standalone
                                                ',
                                                TALLKit::fontSize(size: $size),
                                                TALLKit::height(size: $size),
                                                TALLKit::paddingInline(size: $size),
                                                TALLKit::roundedSize(size: $size),
                                                TALLKit::controlFocusRing(color: $color),
                                            )
                                            ->merge([
                                                'min' => $min ?? null,
                                                'max' => $max ?? null,
                                                'disabled' => $disabled ?: null,
                                            ])
                                            ->merge(['aria-label' => __('Start date')])
                                    }}
                                    type="date"
                                    x-bind:value="value?.start ?? ''"
                                    @change="setRangeBound('start', $event.target.value)"
                                />
                                <span
                                    aria-hidden="true"
                                    {{
                                        $attributes->prefixed('divider:')
                                            ->classes(TALLKit::textNeutral(variant: 'muted'))
                                    }}
                                >&ndash;</span>
                                <input
                                    {{
                                        $attributes->prefixed('end-date:')
                                            ->classes(
                                                '
                                                    flex-1
                                                    tk-field-control-base
                                                    tk-control-standalone
                                                ',
                                                TALLKit::fontSize(size: $size),
                                                TALLKit::height(size: $size),
                                                TALLKit::paddingInline(size: $size),
                                                TALLKit::roundedSize(size: $size),
                                                TALLKit::controlFocusRing(color: $color),
                                            )
                                            ->merge([
                                                'min' => $min ?? null,
                                                'max' => $max ?? null,
                                                'disabled' => $disabled ?: null,
                                            ])
                                            ->merge(['aria-label' => __('End date')])
                                    }}
                                    type="date"
                                    x-bind:value="value?.end ?? ''"
                                    @change="setRangeBound('end', $event.target.value)"
                                />
                            @else
                                <input
                                    {{
                                        $attributes->prefixed('single-date:')
                                            ->classes(
                                                '
                                                    w-full
                                                    tk-field-control-base
                                                    tk-control-standalone
                                                ',
                                                TALLKit::fontSize(size: $size),
                                                TALLKit::height(size: $size),
                                                TALLKit::paddingInline(size: $size),
                                                TALLKit::roundedSize(size: $size),
                                                TALLKit::controlFocusRing(color: $color),
                                            )
                                            ->merge([
                                                'min' => $min ?? null,
                                                'max' => $max ?? null,
                                                'disabled' => $disabled ?: null,
                                            ])
                                            ->merge(['aria-label' => __('Date')])
                                    }}
                                    type="date"
                                    x-bind:value="value ?? ''"
                                    @change="setSingleValue($event.target.value)"
                                />
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if ($clearable !== false || $confirm)
                <div
                    {{
                        $attributes->prefixed('footer:')
                            ->classes(
                                '
                                    p-2
                                    flex items-center justify-end gap-2
                                    border-t border-zinc-100 dark:border-white/10
                                '
                            )
                    }}
                >
                    @if ($clearable !== false)
                        <tk:clearable
                            :attributes="$attributes->prefixed('clearable:')->classes(['me-auto' => $confirm])"
                            :size="$innerSize"
                            :label="is_string($clearable) ? $clearable : 'Clear'"
                            :icon="false"
                        />
                    @endif

                    @if ($confirm)
                        <tk:button
                            :attributes="$attributes->prefixed('cancel:')->merge(['label' => 'Cancel'])"
                            :size="$innerSize"
                            @click="cancel()"
                            variant="none"
                        />

                        <tk:button
                            :attributes="$attributes->prefixed('apply:')->merge(['label' => 'Apply'])"
                            :size="$innerSize"
                            @click="apply()"
                            variant="filled"
                            :$color
                        />
                    @endif
                </div>
            @endif
        </tk:popover>
    </div>
</tk:field.wrapper>
