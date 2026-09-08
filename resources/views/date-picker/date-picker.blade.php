@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'multiple' => null,
    'mode' => null,
    'type' => null,
    'months' => null,
    'min' => null,
    'max' => null,
    'unavailable' => null,
    'minRange' => null,
    'maxRange' => null,
    'withToday' => null,
    'selectableHeader' => null,
    'fixedWeeks' => null,
    'startDay' => null,
    'openTo' => null,
    'forceOpenTo' => null,
    'weekNumbers' => null,
    'locale' => null,
    'clearable' => null,
    'format' => null,
    'withInputs' => null,
    'withConfirmation' => null,
    'withPresets' => null,
    'presets' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext(attributes: $attributes, label: $label, id: $id);

$placeholderText = __(is_string($placeholder) ? $placeholder : 'Select date');
$disabled = (bool) $attributes->get('disabled');
$describedBy = TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError);
$innerSize = TALLKit::adjustSize(size: $size);

$initialCommittedString = match (true) {
    $mode === 'range' => is_array($value) && ($value['start'] ?? null)
        ? (($value['end'] ?? null) ? $value['start'].'/'.$value['end'] : $value['start'])
        : '',
    (bool) $multiple => is_array($value) ? implode(',', $value) : ($value ?? ''),
    default => is_array($value) ? '' : ($value ?? ''),
};

$showInputs = (bool) $withInputs && ! $multiple;
$presetLabels = [
    'today' => 'Today',
    'yesterday' => 'Yesterday',
    'thisWeek' => 'This week',
    'last7Days' => 'Last 7 days',
    'last14Days' => 'Last 14 days',
    'last30Days' => 'Last 30 days',
    'thisMonth' => 'This month',
    'lastMonth' => 'Last month',
    'thisYear' => 'This year',
    'lastYear' => 'Last year',
];
$presetKeys = $withPresets && $mode === 'range'
    ? array_values(array_filter(explode(' ', $presets ?? implode(' ', array_keys($presetLabels)))))
    : [];
$showPresets = count($presetKeys) > 0;

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <div
        wire:ignore
        x-data="datePicker({{ Js::from([
            'value' => $value,
            'multiple' => (bool) $multiple,
            'mode' => $mode,
            'type' => $type,
            'months' => $months,
            'min' => $min,
            'max' => $max,
            'unavailable' => $unavailable,
            'minRange' => $minRange,
            'maxRange' => $maxRange,
            'withToday' => (bool) $withToday,
            'selectableHeader' => (bool) $selectableHeader,
            'fixedWeeks' => (bool) $fixedWeeks,
            'startDay' => $startDay,
            'openTo' => $openTo,
            'forceOpenTo' => (bool) $forceOpenTo,
            'weekNumbers' => (bool) $weekNumbers,
            'locale' => $locale,
            'format' => $format,
            'withConfirmation' => (bool) $withConfirmation,
            'labels' => ['selected' => __('selected')],
        ]) }})"
        {{
            TALLKit::attributesAfter(attributes: $attributes, prefix: 'picker:')
                ->classes('flex-1')
        }}
    >
        <tk:field.control
            :$size
            :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
                ->classes(
                    'tk-control-wrapper-expanded',
                    TALLKit::roundedSize(size: $size, mode: 'large'),
                    TALLKit::controlFocusRingNested(color: $color, expanded: true),
                )
            "
            :icon="$icon ?? 'calendar'"
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
                            'picker:', 'trigger:', 'placeholder:', 'popover:', 'layout:', 'presets:', 'preset:', 'calendar:', 'inputs:',
                            'start-date:', 'end-date:', 'single-date:',
                            'footer:', 'clearable:', 'cancel:', 'apply:',
                        ]))
                }}
            />

            @if ($type === 'input')
                <input
                    {{
                        TALLKit::attributesAfter(attributes: $attributes, prefix: 'trigger:')
                            ->dataKey('input')
                            ->dataKey('control')
                            ->dataKey('group-target')
                            ->merge([
                                'type' => 'text',
                                'autocomplete' => 'off',
                                'id' => $id,
                                'placeholder' => $placeholderText,
                                'aria-describedby' => $describedBy,
                                'aria-invalid' => $invalid ? 'true' : null,
                                'data-invalid' => $invalid ? true : null,
                                'disabled' => $disabled ?: null,
                                'readonly' => $multiple ?: null,
                            ])
                            ->classes(
                                'tk-field-control-base w-full',
                                TALLKit::fontSize(size: $size, mode: 'large'),
                                TALLKit::height(size: $size),
                                TALLKit::paddingStart(size: $size, mode: 'large'),
                                TALLKit::paddingEnd(size: $size, mode: 'large'),
                            )
                            ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: [
                                'picker:', 'placeholder:', 'popover:', 'layout:', 'presets:', 'preset:', 'calendar:', 'inputs:',
                                'start-date:', 'end-date:', 'single-date:',
                                'footer:', 'clearable:', 'cancel:', 'apply:',
                            ]))
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
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'trigger:')
                        ->dataKey('control')
                        ->merge([
                            'id' => $id,
                            'aria-describedby' => $describedBy,
                            'aria-invalid' => $invalid ? 'true' : null,
                            'data-invalid' => $invalid ? true : null,
                        ])
                        ->classes(
                            '
                                [:where(&)]:w-full
                                [:where(&)]:justify-start
                                [:where(&)]:font-normal

                                [:where(&)]:text-zinc-700
                                hover:[:where(&)]:text-zinc-700
                                dark:[:where(&)]:text-zinc-300
                                dark:[:where(&)]:hover:text-zinc-300
                            ',
                            TALLKit::height(size: $size),
                            TALLKit::paddingInline(size: $size, mode: 'large'),
                            TALLKit::fontSize(size: $size, mode: 'large'),
                        )
                        ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: [
                            'picker:', 'placeholder:', 'popover:', 'layout:', 'presets:', 'preset:', 'calendar:', 'inputs:',
                            'start-date:', 'end-date:', 'single-date:',
                            'footer:', 'clearable:', 'cancel:', 'apply:',
                        ]))
                    "
                    variant="none"
                    :$disabled
                    :$size
                >
                    <span
                        x-show="!formatted()"
                        {{
                            TALLKit::attributesAfter(attributes: $attributes, prefix: 'placeholder:')
                                ->classes('text-zinc-400 dark:text-zinc-400')
                        }}
                    >{{ $placeholderText }}</span>
                    <span x-show="formatted()" x-text="formatted()"></span>
                </tk:button>
            @endif
        </tk:field.control>

        <tk:popover
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'popover:')->classes('p-0 max-h-full')"
            :$size
            animation="none"
            keep-open
        >
            <div
                {{
                    TALLKit::attributesAfter(attributes: $attributes, prefix: 'layout:')
                        ->classes(['flex divide-x divide-zinc-100 dark:divide-white/10' => $showPresets])
                }}
            >
                @if ($showPresets)
                    <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'presets:')->classes('flex flex-col gap-0.5 p-1') }}>
                        @foreach ($presetKeys as $key)
                            <tk:button
                                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'preset:')->classes('w-full justify-start')"
                                :size="$innerSize"
                                :label="$presetLabels[$key] ?? $key"
                                ::data-active="isPresetActive('{{ $key }}')"
                                type="button"
                                variant="ghost"
                                @click="applyPreset('{{ $key }}')"
                            />
                        @endforeach
                    </div>
                @endif

                <div class="min-w-0 flex-1">
                    <tk:calendar
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'calendar:')->classes(TALLKit::padding(size: $size))"
                        :$size
                        :$color
                        :$months
                        :$withToday
                        :$selectableHeader
                        :$weekNumbers
                        :static="false"
                        :standalone="false"
                    />

                    @if ($showInputs)
                        <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'inputs:')->classes('flex items-center gap-2 border-t border-zinc-100 p-2 dark:border-white/10') }}>
                            @if ($mode === 'range')
                                <input
                                    {{
                                        TALLKit::attributesAfter(attributes: $attributes, prefix: 'start-date:')
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
                                            ])
                                    }}
                                    type="date"
                                    aria-label="{{ __('Start date') }}"
                                    x-bind:value="value?.start ?? ''"
                                    @change="setRangeBound('start', $event.target.value)"
                                />
                                <span class="text-zinc-400 dark:text-zinc-500" aria-hidden="true">&ndash;</span>
                                <input
                                    {{
                                        TALLKit::attributesAfter(attributes: $attributes, prefix: 'end-date:')
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
                                            ])
                                    }}
                                    type="date"
                                    aria-label="{{ __('End date') }}"
                                    x-bind:value="value?.end ?? ''"
                                    @change="setRangeBound('end', $event.target.value)"
                                />
                            @else
                                <input
                                    {{
                                        TALLKit::attributesAfter(attributes: $attributes, prefix: 'single-date:')
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
                                            ])
                                    }}
                                    type="date"
                                    aria-label="{{ __('Date') }}"
                                    x-bind:value="value ?? ''"
                                    @change="setSingleValue($event.target.value)"
                                />
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if ($clearable !== false || $withConfirmation)
                <div
                    {{
                        TALLKit::attributesAfter(attributes: $attributes, prefix: 'footer:')
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
                            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'clearable:')->classes(['me-auto' => $withConfirmation])"
                            :size="$innerSize"
                            :label="is_string($clearable) ? $clearable : 'Clear'"
                            :icon="false"
                        />
                    @endif

                    @if ($withConfirmation)
                        <tk:button
                            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'cancel:')"
                            :size="$innerSize"
                            @click="cancel()"
                            label="Cancel"
                            variant="none"
                        />

                        <tk:button
                            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'apply:')"
                            :size="$innerSize"
                            @click="apply()"
                            label="Apply"
                            variant="filled"
                            :$color
                        />
                    @endif
                </div>
            @endif
        </tk:popover>
    </div>
</tk:field.wrapper>
