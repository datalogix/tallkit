@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'trigger' => null,
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

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = $attributes->fieldContext(label: $label, id: $id, scope: get_defined_vars());
$value = TALLKit::fieldOldValue($fieldName, $value);
$locked = $attributes->enabled('disabled') || $attributes->enabled('readonly');
$placeholderText = is_string($placeholder) ? __($placeholder) : match ($format) {
    'hexa' => '#00000000',
    'rgba' => 'rgba(0, 0, 0, 0)',
    'hsla' => 'hsla(0, 0%, 0%, 0)',
    default => __('Transparent'),
};

@endphp
<tk:field.wrapper
    :$name
    :attributes="$attributes->withFieldProps(get_defined_vars())"
>
    <div
        wire:ignore.self
        x-data="colorPicker({{
            Js::from([
                'value' => $value,
                'format' => $format
            ])
        }})"
        {{
            $attributes->prefixed('picker:')
                ->classes(['flex-1' => $trigger !== 'button'])
                ->merge(['x-effect' => $live ? "setLive(($live) ?? null)" : false])
        }}
    >
        @if ($trigger === 'button')
            <input
                type="hidden"
                {{
                    $attributes
                        ->dataKey('color-picker')
                        ->merge([
                            'name' => $name,
                            'value' => unless_livewire($value),
                            'wire:model' => $wireModel,
                            'data-invalid' => $invalid ? true : null,
                        ])
                        ->withoutFieldPrefixes([
                            'picker:', 'trigger:',
                            'dropdown:', 'popover:', 'swatch:', 'option:', 'footer:', 'custom:', 'dropper:', 'clearable:',
                        ])
                }}
            />

            <tk:color-picker.button
                :attributes="$attributes->prefixed('trigger:',
                        with: ['dropdown:', 'popover:', 'swatch:', 'option:', 'footer:', 'custom:', 'dropper:', 'clearable:']
                    )
                    ->dataKey('control')
                    ->merge([
                        'variant' => 'subtle',
                        'id' => $id,
                        'aria-describedby' => TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                        ...TALLKit::fieldInvalid($invalid),
                        TALLKit::dataKey('group-target') => true,
                    ])
                    ->mergeDefined(['aria-label' => ! $label && ! $attributes->has('trigger:tooltip') ? 'Pick color' : null])
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
                :$keepOpen
                :disabled="$locked"
                :standalone="false"
            />
        @else
            <tk:field.control
                :$size
                :attributes="$attributes->withFieldControlProps(get_defined_vars())
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
                        :attributes="$attributes->prefixed('trigger:',
                                with: ['dropdown:', 'popover:', 'swatch:', 'option:', 'footer:', 'custom:', 'dropper:', 'clearable:']
                            )
                            ->classes(
                                'shrink-0',
                                TALLKit::roundedSize(size: $size),
                                TALLKit::widthHeight(size: $size),
                            )
                            ->merge([
                                TALLKit::dataKey('group-target') => false,
                            ])
                            ->mergeDefined(['aria-label' => ! $attributes->has('trigger:tooltip') ? 'Pick color' : null])
                        "
                        :disabled="$locked"
                        :icon="false"
                        :$swatches
                        :$clearable
                        :$dropper
                        :$size
                        :$keepOpen
                        :standalone="false"
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
                                'value' => unless_livewire($value),
                                'wire:model' => $wireModel,
                                'aria-describedby' => TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                                ...TALLKit::fieldInvalid($invalid),
                            ])
                            ->withoutFieldPrefixes([
                                'picker:', 'trigger:', 'copyable:',
                                'dropdown:', 'popover:', 'swatch:', 'option:', 'footer:', 'custom:', 'dropper:', 'clearable:',
                            ])
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
                                :attributes="$attributes->prefixed('clearable:')"
                                :$size
                                :label="string_value($clearable)"
                                action="clear()"
                            />
                        @endif

                        @if ($copyable)
                            <tk:copyable
                                :attributes="$attributes->prefixed('copyable:')"
                                :$size
                                :label="string_value($copyable)"
                            />
                        @endif
                    </x-slot:append>
                @endif

            </tk:field.control>
        @endif
    </div>
</tk:field.wrapper>
