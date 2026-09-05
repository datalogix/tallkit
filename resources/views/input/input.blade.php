@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'type' => null,
    'mask' => null,
    'clearable' => null,
    'copyable' => null,
    'viewable' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext(attributes: $attributes, label: $label, id: $id);
$type ??= TALLKit::detectInputType(name: $name);
$mask = TALLKit::detectInputMask(name: $name, mask: $mask, type: $type);
$viewable ??= $type === 'password';
$hasControl = $clearable || $copyable || $viewable || $prepend || $icon || $append || $loading || $iconTrailing || $kbd || $attributes->has('class');

@endphp
@if ($type === 'file')
    <tk:upload :$attributes>{{ $slot }}</tk:upload>
@elseif ($type === 'checkbox')
    <tk:checkbox :$attributes>{{ $slot }}</tk:checkbox>
@elseif ($type === 'radio')
    <tk:radio :$attributes>{{ $slot }}</tk:radio>
@elseif ($type === 'reset' || $type === 'button')
    <tk:button :$attributes :$type>{{ $slot }}</tk:button>
@elseif ($type === 'submit')
    <tk:submit :$attributes>{{ $slot }}</tk:submit>
@elseif ($type === 'range')
    <tk:slider :$attributes>{{ $slot }}</tk:slider>
@else
    <tk:field.wrapper
        :$name
        :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
    >
        <tk:field.control
            :$size
            :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
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
                            'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
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
                            !$hasControl,
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

                    @if ($viewable)
                        <tk:input.viewable
                            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'viewable:')"
                            :$size
                            :label="is_string($viewable) ? $viewable : null"
                        />
                    @endif
                </x-slot:append>
            @endif
        </tk:field.control>
    </tk:field.wrapper>
@endif
