@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'options' => null,
    'scripts' => null,
    'styles' => null,
    'mode' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext(attributes: $attributes, label: $label, id: $id);

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <tk:field.control
        :$size
        :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())"
    >
        <div
            wire:ignore
            x-data="tinymce(
                {{
                    Js::from([
                        'mode' => $mode,
                        'options' => $options ?? [],
                        'scripts' => $scripts ?? [],
                        'styles' => $styles ?? []
                    ])
                }}
            )"
            {{
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'editor:')
                    ->classes('w-full block')
            }}
        >
            <textarea
                {{
                    $attributes
                        ->dataKey('control')
                        ->merge([
                            'name' => $name,
                            'id' => $id,
                            'wire:model' => $wireModel,
                            'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                            'aria-invalid' => $invalid ? 'true' : null,
                            'data-invalid' => $invalid ? true : null,
                        ])
                        ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: ['editor:']))
                }}
            >{{ in_livewire() ? null : ($value ?? $slot) }}</textarea>
        </div>
    </tk:field.control>
</tk:field.wrapper>
