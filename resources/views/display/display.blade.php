@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = $attributes->fieldContext(label: $label, id: $id, scope: get_defined_vars());
$hasControl = $prepend || $icon || $append || $loading || $iconTrailing || $kbd || $attributes->has('class');

@endphp
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
                        'aria-describedby' => TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                        ...TALLKit::fieldInvalid($invalid),
                    ])
                    ->withoutFieldPrefixes(['display:'])
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
                        TALLKit::paddingStart(size: $size, mode: 'large'),
                        TALLKit::paddingEnd(size: $size, mode: 'large'),
                        TALLKit::generateClassBySize(size: $size, name: 'min-h', values: ['8', '9', '10', '12', '14', '16', '18']),
                        $attributes->pluck('display:class'),
                    )
                    ->when(
                        ! $hasControl,
                        fn ($attrs) => $attrs->classes(
                            'tk-control-standalone-no-disabled',
                            TALLKit::roundedSize(size: $size, mode: 'large'),
                        ),
                    )
            }}
        >{{ $value ?? (($model = $attributes->whereStartsWith('wire:model')->first() ?: $wireModel) ? data_get(TALLKit::livewireComponent(), $model) : null) ?? $slot }}</output>
    </tk:field.control>
</tk:field.wrapper>
