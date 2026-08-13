@props([
    ...TALLKit::fieldProps(),
    'multiple' => null,
    'droppable' => null,
    'accept' => null,
    'maxSize' => null,
    'maxFiles' => null,
    'sortable' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext($attributes, $label, $id);

$sortable ??= (bool) $multiple;

$files = TALLKit::getUploadedFiles($value ?? ((in_livewire() && property_exists($this, $fieldName)) ? data_get($this, $fieldName) : null));

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <div
        wire:ignore
        data-tallkit-control
        class="flex flex-col gap-4"
        @if ($invalid) data-invalid aria-invalid="true" @endif
        :data-invalid="isInvalid || null"
        :aria-invalid="isInvalid ? 'true' : null"
        x-data="upload({
            wireModel: @js($wireModel),
            multiple: @js((bool) $multiple),
            droppable: @js($droppable ?? true),
            maxSize: @js($maxSize ?: null),
            maxFiles: @js($maxFiles ?: null),
            sortable: @js($sortable),
            invalid: @js((bool) $invalid),
            files: @js($files),
            tooLargeMessage: @js(__('This file is too large.')),
            invalidTypeMessage: @js(__('This file type is not allowed.')),
            tooManyFilesMessage: @js(__('Too many files selected.')),
        })"
    >
        <input
            @if ($name) name="{{ $name }}" @endif
            @if ($id) id="{{ $id }}" @endif
            @if ($accept) accept="{{ $accept }}" @endif
            @if ($multiple) multiple @endif
            {{ $attributes->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes([
                    'button:', 'remove:', 'cancel:', 'retry:', 'edit:', 'view:',
                ]))
                ->merge(['aria-describedby' => TALLKit::ariaDescribedBy($id, $description, $help, $invalid, $showError)])
                ->classes('hidden')
            }}
            type="file"
            x-ref="fileInput"
        />

        <tk:upload.progress />

        <tk:upload.dropzone :rootAttributes="$attributes" />

        <tk:upload.hint :$accept :$maxSize :$maxFiles />
    </div>
</tk:field.wrapper>
