@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'type' => null,
    'multiple' => null,
    'interval' => null,
    'min' => null,
    'max' => null,
    'unavailable' => null,
    'openTo' => null,
    'format' => null,
    'locale' => null,
    'withoutDropdown' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext(attributes: $attributes, label: $label, id: $id);

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <div
        x-data="timePicker({{
            Js::from([
                'value' => $value,
                'multiple' => (bool) $multiple,
                'format' => $format,
                'locale' => $locale,
                'interval' => max(1, (int) ($interval ?? 30)),
                'min' => $min,
                'max' => $max,
                'unavailable' => $unavailable,
                'openTo' => $openTo,
                'type' => $withoutDropdown ? 'input' : $type,
            ])
        }})"
        {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'picker:')->classes('contents') }}
    >
        <tk:field.control
            :$size
            :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
                ->classes(
                    'tk-control-wrapper-expanded',
                    TALLKit::roundedSize(size: $size, mode: 'large'),
                    TALLKit::controlFocusRingNested(color: $color, expanded: true),
                )
                ->merge([
                    'icon' => $icon ?? 'clock-outline',
                    'icon:size' => TALLKit::adjustSize(size: $size),
                    'iconTrailing' => $withoutDropdown ? null : 'chevron-down',
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
                            'picker:', 'trigger:', 'popover:', 'list:', 'slot:', 'footer:',
                        ]))
                }}
            />

            @if ($type === 'input' || $withoutDropdown)
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
                                'placeholder' => __(is_string($placeholder) ? $placeholder : 'Select time'),
                                'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                                'aria-invalid' => $invalid ? 'true' : null,
                                'data-invalid' => $invalid ? true : null,
                                'disabled' => (bool) $attributes->get('disabled') ?: null,
                                'readonly' => $multiple ?: null,
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
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'trigger:')
                        ->dataKey('control')
                        ->merge([
                            'id' => $id,
                            'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
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
                            'picker:', 'popover:', 'list:', 'slot:', 'footer:',
                        ]))
                    "
                    variant="none"
                    :disabled="(bool) $attributes->get('disabled')"
                    :$size
                >
                    <span
                        x-show="!formatted()"
                        {{
                            TALLKit::attributesAfter(attributes: $attributes, prefix: 'placeholder:')
                                ->classes('text-zinc-400 dark:text-zinc-400')
                        }}
                    >{{ __(is_string($placeholder) ? $placeholder : 'Select time') }}</span>
                    <span x-show="formatted()" x-text="formatted()"></span>
                </tk:button>
            @endif
        </tk:field.control>

        @unless ($withoutDropdown)
            <tk:popover
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'popover:')->classes('p-0')"
                :$size
                animation="none"
                :keep-open="(bool) $multiple"
            >
                <div
                    role="listbox"
                    aria-label="{{ __('Time') }}"
                    aria-multiselectable="{{ $multiple ? 'true' : 'false' }}"
                    {{
                        TALLKit::attributesAfter(attributes: $attributes, prefix: 'list:')
                            ->classes('p-1 space-y-0.5')
                    }}
                >
                    <template x-for="slot in slots()" :key="slot">
                        <tk:button
                            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'slot:')
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
