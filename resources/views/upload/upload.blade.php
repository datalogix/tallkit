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
$previewName = TALLKit::generateId('upload-preview');

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <div
        wire:ignore
        x-cloak
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
            previewName: @js($previewName),
        })"
        :data-invalid="isInvalid || null"
        :aria-invalid="isInvalid ? 'true' : null"
        {{
            TALLKit::attributesAfter($attributes, 'control:')
                ->dataKey('control')
                ->classes('flex flex-col gap-4')
                ->merge([
                    'aria-invalid' => $invalid ? 'true' : null,
                    'data-invalid' => $invalid ? true : null,
                    'aria-invalid' => $invalid ? 'true' : null,
                ])
        }}
    >
        <input
            {{
                $attributes->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes([
                    'dropzone:', 'progress:', 'tile:', 'hint:', 'modal:',
                ]))->merge([
                    'name' => $name,
                    'id' => $id,
                    'accept' => $accept,
                    'multiple' => $multiple,
                    'aria-describedby' => TALLKit::ariaDescribedBy($id, $description, $help, $invalid, $showError)
                ])
                ->classes('hidden')
            }}
            type="file"
            x-ref="fileInput"
        />

        <tk:upload.dropzone
            :attributes="TALLKit::attributesAfter($attributes, 'dropzone:')"
            :$size
            :$multiple
        >
            <template x-if="multiple && activeFiles.length > 1">
                <tk:progress
                    :attributes="TALLKit::attributesAfter($attributes, 'progress:')"
                    variable="aggregateProgress"
                />
            </template>

            <template x-for="(file, index) in files" :key="file.id">
                <tk:upload.tile
                    :attributes="TALLKit::attributesAfter($attributes, 'tile:')"
                    :$size
                    :$multiple
                />
            </template>
        </tk:upload.dropzone>

        <tk:upload.hint
            :attributes="TALLKit::attributesAfter($attributes, 'hint:')"
            :$size
            :$accept
            :$maxSize
            :$maxFiles
        />

        <tk:upload.modal
            :attributes="TALLKit::attributesAfter($attributes, 'modal:')"
            :name="$previewName"
            :$size
        />
    </div>
</tk:field.wrapper>
