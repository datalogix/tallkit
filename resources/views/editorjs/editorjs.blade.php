@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'options' => null,
    'scripts' => null,
    'styles' => null,
    'toolbar' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = $attributes->fieldContext(label: $label, id: $id, scope: get_defined_vars());
$value = TALLKit::fieldOldValue($fieldName, $value);

@endphp
<tk:field.wrapper
    :$name
    :attributes="$attributes->withFieldProps(get_defined_vars())"
>
    <tk:field.control
        :$size
        :attributes="$attributes->withFieldControlProps(get_defined_vars())"
    >
        <div
            wire:ignore
            x-data="editorjs({{ Js::from([
                'toolbar' => $toolbar,
                'options' => $options ?? [],
                'scripts' => $scripts ?? [],
                'styles' => $styles ?? [],
                'i18n' => TALLKit::editorTranslations('editorjs'),
            ]) }})"
            {{
                $attributes->prefixed('editor:')
                    ->classes(
                        '
                            tk-control-surface
                            tk-control-invalid-border-nested
                            w-full block
                            text-zinc-700 dark:text-zinc-200
                            outline-none
                            focus-within:tk-focus-ring
                        ',
                        TALLKit::roundedSize(size: $size, mode: 'large'),
                        TALLKit::paddingBlock(size: $size, mode: 'large'),
                        TALLKit::paddingInline(size: $size, mode: 'large'),
                    )
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
                            'aria-describedby' => TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                            ...TALLKit::fieldInvalid($invalid),
                        ])
                        ->withoutFieldPrefixes(['editor:'])
                        ->class('hidden')
                }}
            >{{ unless_livewire($value ?? $slot) }}</textarea>

            <div x-ref="root" role="group" @if ($label) aria-labelledby="{{ $id }}-label" @endif></div>
        </div>
    </tk:field.control>
</tk:field.wrapper>
