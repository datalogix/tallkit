@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'type' => null,
    'mask' => null,
    'clearable' => null,
    'copyable' => null,
    'viewable' => null,
    'old' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::fieldContext(attributes: $attributes, label: $label, id: $id, scope: get_defined_vars());
$type ??= TALLKit::fieldType(name: $name);
$mask = TALLKit::fieldMask(name: $name, mask: $mask, type: $type);
$viewable ??= $type === 'password';
$value = $old === false ? $value : TALLKit::fieldOldValue($fieldName, $value, $type);
$value = match ($type) {
    'date' => TALLKit::fieldDateValue($value),
    'datetime-local' => TALLKit::fieldDateValue($value, 'Y-m-d\TH:i'),
    'time' => TALLKit::fieldDateValue($value, 'H:i'),
    'month' => TALLKit::fieldDateValue($value, 'Y-m'),
    default => $value,
};
$hasControl = $clearable || $copyable || $viewable || $prepend || $icon || $append || $loading || $iconTrailing || $kbd || $attributes->has('class');

@endphp
@if ($type === 'file')
    <tk:upload :attributes="TALLKit::fieldWithProps($attributes, get_defined_vars())">{{ $slot }}</tk:upload>
@elseif ($type === 'checkbox')
    <tk:checkbox :attributes="TALLKit::fieldWithProps($attributes, get_defined_vars())">{{ $slot }}</tk:checkbox>
@elseif ($type === 'radio')
    <tk:radio :attributes="TALLKit::fieldWithProps($attributes, get_defined_vars())">{{ $slot }}</tk:radio>
@elseif ($type === 'reset' || $type === 'button')
    <tk:button :attributes="TALLKit::fieldWithProps($attributes, get_defined_vars())" :$type>{{ $slot }}</tk:button>
@elseif ($type === 'submit')
    <tk:submit :attributes="TALLKit::fieldWithProps($attributes, get_defined_vars())">{{ $slot }}</tk:submit>
@elseif ($type === 'range')
    <tk:slider :attributes="TALLKit::fieldWithProps($attributes, get_defined_vars())">{{ $slot }}</tk:slider>
@else
    <tk:field.wrapper
        :$name
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
    >
        <tk:field.control
            :$size
            :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
                ->when(
                    $hasControl,
                    fn ($attrs) => $attrs->classes(
                        'tk-control-wrapper',
                        TALLKit::roundedSize(size: $size, mode: 'large'),
                        TALLKit::controlFocusRingNested(color: $color),
                    ),
                )
            "
        >
            <input
                {{
                    $attributes
                        ->dataKey('input')
                        ->dataKey('control')
                        ->dataKey('group-target')
                        ->merge([
                            'type' => $type,
                            'name' => $name,
                            'id' => $id,
                            'value' => in_livewire() ? null : ($value ?? $slot),
                            'placeholder' => $placeholder ? __((string) $placeholder) : null,
                            'wire:model' => $wireModel,
                            'x-data' => $mask ? true : null,
                            'x-mask' => $mask,
                            'inputmode' => $mask && $type === 'text' && preg_match('/^[^a-zA-Z*]*$/', $mask) ? 'numeric' : null,
                            'aria-describedby' => TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                            'aria-invalid' => $invalid ? 'true' : null,
                            'data-invalid' => $invalid ? true : null,
                        ])
                        ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: ['input:', 'clearable:', 'copyable:', 'viewable:']))
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
                            match ($type) {
                                'color' => $prepend || $icon ? '' : 'ps-1 pe-1',
                                default => '',
                            },
                            $attributes->pluck('input:class'),
                         )
                         ->when(
                            ! $hasControl,
                            fn ($attrs) => $attrs->classes(
                                'tk-control-standalone',
                                TALLKit::roundedSize(size: $size, mode: 'large'),
                                TALLKit::controlFocusRing(color: $color),
                            ),
                        )
                }}
            />

            @if ($clearable || $copyable || $viewable)
                <x-slot:append>
                    {{ $append ?? '' }}

                    @if ($clearable)
                        <tk:clearable
                            :attributes="$attributes->prefixed('clearable:')"
                            :$size
                            :label="is_string($clearable) ? $clearable : null"
                        />
                    @endif

                    @if ($copyable)
                        <tk:copyable
                            :attributes="$attributes->prefixed('copyable:')"
                            :$size
                            :label="is_string($copyable) ? $copyable : null"
                        />
                    @endif

                    @if ($viewable)
                        <tk:input.viewable
                            :attributes="$attributes->prefixed('viewable:')"
                            :$size
                            :label="is_string($viewable) ? $viewable : null"
                        />
                    @endif
                </x-slot:append>
            @endif
        </tk:field.control>
    </tk:field.wrapper>
@endif
