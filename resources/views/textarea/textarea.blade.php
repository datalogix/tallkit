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

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext(attributes: $attributes, label: $label, id: $id);
$hasControl = $prepend || $icon || $append || $loading || $iconTrailing || $kbd || $copyable || $attributes->has('class');
$maxlength = (int) $maxlength;
$hasCounter = (bool) ($counter ?? $maxlength);
$initialLength = mb_strlen((string) (in_livewire() ? null : ($value ?? $slot)));
$counterExpression = $maxlength ? sprintf("length + ' / %d'", $maxlength) : 'length';

@endphp
@if ($hasCounter || $maxRows)
    <div
        x-data="textarea({ maxRows: @js($maxRows), counter: @js($hasCounter), length: @js($initialLength) })"
        {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'counter:') }}
    >
@endif
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
                        'wire:model' => $wireModel,
                        'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                        'aria-invalid' => $invalid ? 'true' : null,
                        'data-invalid' => $invalid ? true : null,
                    ])
                    ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: ['counter:', 'textarea:', 'copyable:', 'counter:']))
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
                        !$hasControl,
                        fn ($attrs) => $attrs->classes(
                            'tk-control-standalone',
                            TALLKit::roundedSize(size: $size, mode: 'large'),
                            TALLKit::controlFocusRing(color: $color),
                        ),
                    )
            }}
        >{{ in_livewire() ? null : ($value ?? $slot) }}</textarea>

        @if ($copyable)
            <x-slot:append>
                {{ $append ?? '' }}

                <tk:copyable
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'copyable:')"
                    :$size
                    :label="is_string($copyable) ? $copyable : null"
                />
            </x-slot:append>
        @endif
    </tk:field.control>
</tk:field.wrapper>
    @if ($hasCounter)
        <tk:text
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'counter:')->classes('text-end mt-1.5')"
            :size="TALLKit::adjustSize(size: $size)"
            variant="subtle"
            x-text="{{ $counterExpression }}"
        >{{ $maxlength ? $initialLength.' / '. $maxlength : $initialLength }}</tk:text>
    @endif
@if ($hasCounter || $maxRows)
    </div>
@endif
