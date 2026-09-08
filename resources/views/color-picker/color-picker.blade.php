@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'type' => null,
    'format' => null,
    'preview' => null,
    'swatches' => null,
    'clearable' => null,
    'copyable' => null,
    'dropper' => null,
    'live' => null,
    'keepOpen' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext(attributes: $attributes, label: $label, id: $id);
$disabled = (bool) $attributes->get('disabled');
$placeholderText = is_string($placeholder) ? __($placeholder) : match ($format) {
    'hexa' => '#00000000',
    'rgba' => 'rgba(0, 0, 0, 0)',
    'hsla' => 'hsla(0, 0%, 0%, 0)',
    default => __('Transparent'),
};

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <div
        wire:ignore.self
        x-data="colorPicker({{ Js::from(['value' => $value, 'format' => $format]) }})"
        {{
            TALLKit::attributesAfter(attributes: $attributes, prefix: 'picker:')
                ->classes(['flex-1' => $type !== 'button'])
                ->merge(['x-effect' => $live ? "value = ($live) ?? null" : false])
        }}
    >
        @if ($type === 'button')
            <input
                type="hidden"
                {{
                    $attributes
                        ->dataKey('color-picker')
                        ->merge([
                            'name' => $name,
                            'value' => in_livewire() ? null : $value,
                            'wire:model' => $wireModel,
                            'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                            'aria-invalid' => $invalid ? 'true' : null,
                            'data-invalid' => $invalid ? true : null,
                        ])
                        ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: [
                            'picker:', 'trigger:',
                            'dropdown:', 'popover:', 'swatch:', 'option:', 'footer:', 'custom:', 'dropper:', 'clearable:',
                        ]))
                }}
            />

            <tk:color-picker.button
                :attributes="TALLKit::attributesAfter(
                        attributes: $attributes,
                        prefix: 'trigger:',
                        prepend: ['dropdown:', 'popover:', 'swatch:', 'option:', 'footer:', 'custom:', 'dropper:', 'clearable:']
                    )
                    ->dataKey('control')
                    ->merge([
                        'id' => $id,
                        'aria-invalid' => $invalid ? 'true' : null,
                        'data-invalid' => $invalid ? true : null,
                        TALLKit::dataKey('group-target') => true,
                    ])
                    ->classes(
                        TALLKit::roundedSize(size: $size, mode: 'large'),
                        TALLKit::widthHeight(size: $size, mode: 'large'),
                        TALLKit::controlFocusRing(color: $color, expanded: true),
                        match ($preview) {
                            'underline' => 'p-1.5! w-auto h-auto tk-control-focus-ring-expanded',
                            default => '
                                tk-control-standalone-expanded
                                tk-control-invalid-border
                            ',
                        },
                    )
                "
                :$preview
                :$icon
                :$swatches
                :$clearable
                :$dropper
                :$size
                :$disabled
                :$keepOpen
                variant="subtle"
            />
        @else
            <tk:field.control
                :$size
                :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
                    ->classes(
                        'tk-control-wrapper-expanded',
                        TALLKit::roundedSize(size: $size, mode: 'large'),
                        TALLKit::controlFocusRingNested(color: $color, expanded: true),
                    )
                "
            >
                <x-slot:prepend>
                    {{ $prepend ?? '' }}

                    <tk:color-picker.button
                        :attributes="TALLKit::attributesAfter(
                                attributes: $attributes,
                                prefix: 'trigger:',
                                prepend: ['dropdown:', 'popover:', 'swatch:', 'option:', 'footer:', 'custom:', 'dropper:', 'clearable:']
                            )
                            ->classes(
                                'shrink-0',
                                TALLKit::roundedSize(size: $size),
                                TALLKit::widthHeight(size: $size),
                            )
                            ->merge([
                                TALLKit::dataKey('group-target') => false,
                            ])
                        "
                        :icon="false"
                        :$swatches
                        :$clearable
                        :$dropper
                        :$size
                        :$disabled
                        :$keepOpen
                    />
                </x-slot:prepend>

                <input
                    type="text"
                    autocomplete="off"
                    placeholder="{{ $placeholderText }}"
                    @focus="$el.select()"
                    @blur="commitTyped($el.value)"
                    @keydown.enter.prevent="commitTyped($el.value); $el.blur()"
                    {{
                        $attributes
                            ->dataKey('color-picker')
                            ->dataKey('input')
                            ->dataKey('control')
                            ->dataKey('group-target')
                            ->merge([
                                'name' => $name,
                                'id' => $id,
                                'value' => in_livewire() ? null : $value,
                                'wire:model' => $wireModel,
                                'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                                'aria-invalid' => $invalid ? 'true' : null,
                                'data-invalid' => $invalid ? true : null,
                            ])
                            ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: [
                                'picker:', 'copyable:',
                                'dropdown:', 'popover:', 'swatch:', 'option:', 'footer:', 'custom:', 'dropper:', 'clearable:',
                            ]))
                            ->except('class')
                            ->classes(
                                '
                                    tk-field-control-base
                                    peer
                                ',
                                TALLKit::fontSize(size: $size, mode: 'large'),
                                TALLKit::height(size: $size),
                                TALLKit::paddingStart(size: $size, mode: 'large'),
                                TALLKit::paddingEnd(size: $size, mode: 'large'),
                                $attributes->pluck('input:class'),
                            )
                    }}
                />

                @if ($clearable || $copyable)
                    <x-slot:append>
                        {{ $append ?? '' }}

                        @if ($clearable)
                            <tk:clearable
                                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'clearable:')"
                                :$size
                                :label="is_string($clearable) ? $clearable : null"
                            />
                        @endif

                        @if ($copyable)
                            <tk:copyable
                                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'copyable:')"
                                :$size
                                :label="is_string($copyable) ? $copyable : null"
                            />
                        @endif
                    </x-slot:append>
                @endif

            </tk:field.control>
        @endif
    </div>
</tk:field.wrapper>
