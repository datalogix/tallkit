@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'options' => null,
    'scripts' => null,
    'styles' => null,
    'toolbar' => null,
    'upload' => null,
])
@php

$upload = TALLKit::editorUpload($upload);

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
            x-data="tinymce(
                {{
                    Js::from([
                        'toolbar' => $toolbar,
                        'options' => $options ?? [],
                        'scripts' => $scripts ?? [],
                        'styles' => $styles ?? [],
                        'upload' => $upload,
                        'title' => $label ? __($label) : null,
                        'messages' => TALLKit::editorUploadMessages(),
                        'locale' => TALLKit::resolveLocale(),
                    ])
                }}
            )"
            {{
                $attributes->prefixed('editor:')
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
                            'aria-describedby' => TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                            ...TALLKit::fieldInvalid($invalid),
                        ])
                        ->withoutFieldPrefixes(['editor:'])
                }}
            >{{ unless_livewire($value ?? $slot) }}</textarea>
        </div>
    </tk:field.control>
</tk:field.wrapper>
