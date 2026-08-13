@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'resize' => null,
    'rows' => null,
    'maxRows' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext($attributes, $label, $id);
$hasControl = $prepend || $icon || $append || $loading || $iconTrailing || $kbd || $attributes->has('class');

@endphp
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
                ),
            )
        "
    >
        <textarea
            x-data="textarea(@js($maxRows))"
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
                        'aria-describedby' => TALLKit::ariaDescribedBy($id, $description, $help, $invalid, $showError),
                        'aria-invalid' => $invalid ? 'true' : null,
                        'data-invalid' => $invalid ? true : null,
                    ])
                    ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes([
                        'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:',
                        'textarea:',
                    ]))
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
                        match ($maxRows ? 'none' : $resize) {
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
                        ),
                    )
            }}
        >{{ in_livewire() ? null : ($value ?? $slot) }}</textarea>
    </tk:field.control>
</tk:field.wrapper>
