@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'resize' => null,
    'rows' => null,
    'maxlength' => null,
    'maxRows' => null,
    'copyable' => null,
    'counter' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = $attributes->fieldContext(label: $label, id: $id, scope: get_defined_vars());
$value = TALLKit::fieldOldValue($fieldName, $value);
$hasControl = has_content($prepend, $icon, $append) || $loading || $iconTrailing || $kbd || $copyable || $attributes->has('class');
$maxlength = (int) $maxlength;
$hasCounter = (bool) ($counter ?? $maxlength);
$initialLength = mb_strlen((string) unless_livewire($value ?? $slot));
$counterExpression = $maxlength ? sprintf("length + ' / %d'", $maxlength) : 'length';

@endphp
@if ($hasCounter || $maxRows)
    <div
        x-data="textarea({{ Js::from([
            'maxRows' => $maxRows,
            'counter' => $hasCounter,
            'length' => $initialLength,
        ]) }})"
        {{ $attributes->prefixed('area:') }}
    >
@endif
<tk:field.wrapper
    :$name
    :attributes="$attributes->withFieldProps(get_defined_vars())"
>
    <tk:field.control
        :$size
        :attributes="$attributes->withFieldControlProps(get_defined_vars())
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
        <textarea
            {{
                $attributes
                    ->dataKey('textarea')
                    ->dataKey('control')
                    ->dataKey('group-target')
                    ->merge([
                        'name' => $name,
                        'id' => $id,
                        'placeholder' => $placeholder ? __((string) $placeholder) : null,
                        'rows' => is_numeric($rows) || $rows === null ? ($rows ?? 3) : null,
                        'maxlength' => $maxlength ?: null,
                        'wire:model' => $wireModel,
                        'aria-describedby' => collect([
                            TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                            $hasCounter && $id ? $id.'-counter' : null,
                        ])->filter()->implode(' ') ?: null,
                        ...TALLKit::fieldInvalid($invalid),
                    ])
                    ->withoutFieldPrefixes(['area:', 'counter:', 'textarea:', 'copyable:'])
                    ->except('class')
                    ->classes(['field-sizing-content' => $rows === 'auto'])
                    ->classes(
                        '
                            tk-field-control-base
                            peer
                        ',
                        TALLKit::fontSize(size: $size, mode: 'large'),
                        TALLKit::paddingBlock(size: $size, mode: 'large'),
                        TALLKit::paddingStart(size: $size, mode: 'large'),
                        TALLKit::paddingEnd(size: $size, mode: 'large'),
                        match (($maxRows || $rows === 'auto') ? 'none' : $resize) {
                            'none' => 'resize-none',
                            'both' => 'resize',
                            'horizontal' => 'resize-x',
                            default => 'resize-y',
                        },
                        $attributes->pluck('textarea:class'),
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
        >{{ unless_livewire($value ?? $slot) }}</textarea>

        @if ($copyable)
            <x-slot:append>
                {{ $append ?? '' }}

                <tk:copyable
                    :attributes="$attributes->prefixed('copyable:')"
                    :$size
                    :label="string_value($copyable)"
                />
            </x-slot:append>
        @endif
    </tk:field.control>
</tk:field.wrapper>
    @if ($hasCounter)
        <tk:text
            :attributes="$attributes->prefixed('counter:')->classes('text-end mt-1.5')"
            :id="$id ? $id.'-counter' : null"
            :size="TALLKit::adjustSize(size: $size)"
            variant="subtle"
            x-text="{{ $counterExpression }}"
        >{{ $maxlength ? $initialLength.' / '. $maxlength : $initialLength }}</tk:text>
    @endif
@if ($hasCounter || $maxRows)
    </div>
@endif
