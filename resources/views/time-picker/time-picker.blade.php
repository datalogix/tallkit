@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'trigger' => null,
    'multiple' => null,
    'interval' => null,
    'min' => null,
    'max' => null,
    'unavailable' => null,
    'openTo' => null,
    'format' => null,
    'locale' => null,
    'dropdown' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::fieldContext(attributes: $attributes, label: $label, id: $id, scope: get_defined_vars());
$value = TALLKit::fieldOldValue($fieldName, $value);

$value = TALLKit::fieldDateValue($value, 'H:i');
$min = TALLKit::fieldDateValue($min, 'H:i');
$max = TALLKit::fieldDateValue($max, 'H:i');
$unavailable = TALLKit::fieldDateValue($unavailable, 'H:i');
$openTo = TALLKit::fieldDateValue($openTo, 'H:i');
$locale = TALLKit::resolveLocale($locale);

$disabled = TALLKit::isAttributeEnabled($attributes->get('disabled'));
$readonly = TALLKit::isAttributeEnabled($attributes->get('readonly'));

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <div
        wire:ignore
        x-data="timePicker(@js([
                'value' => $value,
                'multiple' => (bool) $multiple,
                'format' => $format,
                'locale' => $locale,
                'interval' => max(1, (int) ($interval ?? 30)),
                'min' => $min,
                'max' => $max,
                'unavailable' => $unavailable,
                'openTo' => $openTo,
                'trigger' => $dropdown === false ? 'input' : $trigger,
            ]))"
        {{ $attributes->prefixed('picker:')->classes('contents') }}
    >
        <tk:field.control
            :$size
            :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
                ->classes(
                    'tk-control-wrapper-expanded',
                    TALLKit::roundedSize(size: $size, mode: 'large'),
                    TALLKit::controlFocusRingNested(color: $color, expanded: true),
                )
                ->merge([
                    'icon' => $icon ?? 'clock-outline',
                    'icon:size' => TALLKit::adjustSize(size: $size),
                    'iconTrailing' => $dropdown === false ? null : 'chevron-down',
                    'icon-trailing:size' => TALLKit::adjustSize(size: $size),
                ])
            "
        >
            <input
                type="hidden"
                {{
                    $attributes
                        ->dataKey('time-picker')
                        ->merge([
                            'name' => $name,
                            'value' => in_livewire() ? null : (is_array($value) ? implode(',', $value) : $value),
                            'wire:model' => $wireModel,
                        ])
                        ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: [
                            'picker:', 'trigger:', 'placeholder:', 'formatted:', 'popover:', 'list:', 'slot:',
                        ]))
                }}
            />

            @if ($trigger === 'input' || $dropdown === false)
                <input
                    {{
                        $attributes->prefixed('trigger:')
                            ->dataKey('input')
                            ->dataKey('control')
                            ->dataKey('group-target')
                            ->merge([
                                'type' => 'text',
                                'role' => $dropdown === false ? null : 'combobox',
                                'aria-haspopup' => $dropdown === false ? null : 'listbox',
                                'autocomplete' => 'off',
                                'id' => $id,
                                'placeholder' => __(is_string($placeholder) ? $placeholder : 'Select time'),
                                'aria-describedby' => TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
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
                            'aria-describedby' => TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
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
                    >{{ __(is_string($placeholder) ? $placeholder : 'Select time') }}</span>
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

        @unless ($dropdown === false)
            <tk:popover
                :attributes="$attributes->prefixed('popover:')->classes('p-0')"
                :$size
                animation="none"
                :keep-open="(bool) $multiple"
            >
                <div
                    role="listbox"
                    aria-multiselectable="{{ $multiple ? 'true' : 'false' }}"
                    @keydown="moveSlotFocus($event)"
                    {{
                        $attributes->prefixed('list:')
                            ->classes('p-1 space-y-0.5')
                            ->merge(['aria-label' => __('Time')])
                    }}
                >
                    <template x-for="slot in slots()" :key="slot">
                        <tk:button
                            :attributes="$attributes->prefixed('slot:')
                                ->classes(
                                    '
                                        w-full justify-center tabular-nums

                                        [&[data-unavailable]]:disabled:opacity-40
                                        [&[data-unavailable]]:disabled:line-through
                                    '
                                )
                            "
                            variant="ghost"
                            :size="$size"
                            role="option"
                            tabindex="-1"
                            ::data-slot="slot"
                            ::aria-selected="isSelected(slot)"
                            ::data-active="isSelected(slot)"
                            ::data-unavailable="isTimeDisabled(slot)"
                            ::disabled="isTimeDisabled(slot)"
                            @click="select(slot)"
                        >
                            <span x-text="formatSlot(slot)"></span>
                        </tk:button>
                    </template>
                </div>
            </tk:popover>
        @endunless
    </div>
</tk:field.wrapper>
