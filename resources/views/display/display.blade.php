@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
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
                    'tk-control-wrapper-no-disabled',
                    TALLKit::roundedSize(size: $size, mode: 'large'),
                ),
            )
        "
    >
        <output
            tabindex="0"
            {{
                $attributes
                    ->dataKey('display')
                    ->dataKey('control')
                    ->dataKey('group-target')
                    ->merge([
                        'name' => $name,
                        'id' => $id,
                        'wire:model' => $wireModel,
                        'aria-describedby' => TALLKit::ariaDescribedBy($id, $description, $help, $invalid, $showError),
                        'aria-invalid' => $invalid ? 'true' : null,
                        'data-invalid' => $invalid ? true : null,
                    ])
                    ->whereDoesntStartWith([
                        'field:', 'label:', 'info:', 'badge:', 'description:',
                        'group:', 'prefix:', 'suffix:',
                        'help:', 'error:',
                        'control:', 'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:',
                        'display:',
                    ])
                    ->except('class')
                    ->classes(
                        '
                            block
                            w-full
                            min-w-0
                            flex-1
                            peer
                            [print-color-adjust:exact]

                            whitespace-pre-wrap
                            break-words

                            text-zinc-700
                            dark:text-zinc-300
                        ',
                        TALLKit::fontSize(size: $size, mode: 'large'),
                        TALLKit::paddingBlock(size: $size, mode: 'large'),
                        TALLKit::paddingInline(size: $size, mode: 'large'),
                        TALLKit::paddingStart(size: $size, mode: 'large'),
                        TALLKit::paddingEnd(size: $size, mode: 'large'),
                        TALLKit::generateClassBySize(size: $size, name: 'min-h', values: ['8', '9', '10', '12', '14', '16', '18']),
                        $attributes->pluck('display:class'),
                    )
                    ->when(
                        !$hasControl,
                        fn ($attrs) => $attrs->classes(
                            'tk-control-standalone-no-disabled',
                            TALLKit::roundedSize(size: $size, mode: 'large'),
                        ),
                    )
            }}
        >{{ $value ?? $slot }}</output>
    </tk:field.control>
</tk:field.wrapper>
