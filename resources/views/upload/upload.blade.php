@props([
    ...TALLKit::fieldProps(),
    'multiple' => null,
    'droppable' => null,
    'accept' => null,
    'maxSize' => null,
    'maxFiles' => null,
    'sortable' => null,
    'variant' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext(attributes: $attributes, label: $label, id: $id);
$variant ??= 'dropzone';
if ($variant === 'avatar') {
    $multiple = false;
    $maxFiles = null;
}
$sortable ??= (bool) $multiple && ! in_array($variant, ['avatar', 'button']);
$files = TALLKit::getUploadedFiles(value: $value ?? ((in_livewire() && property_exists($this, $fieldName)) ? data_get($this, $fieldName) : null));
$previewName = TALLKit::generateId(prefix: 'upload-preview');

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
        :data-invalid="isInvalid() || null"
        :aria-invalid="isInvalid() ? 'true' : null"
        {{
            TALLKit::attributesAfter(attributes: $attributes, prefix: 'control:')
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
                $attributes->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: [
                    'dropzone:', 'progress:', 'tile:', 'hint:', 'modal:',
                ]))->merge([
                    'name' => $name,
                    'id' => $id,
                    'accept' => $accept,
                    'multiple' => $multiple,
                    'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError)
                ])
                ->classes('hidden')
            }}
            type="file"
            x-ref="fileInput"
        />

        <tk:upload.dropzone
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'dropzone:')"
            :$size
            :$multiple
            :$variant
            :$color
        >
            @if ($variant === 'avatar')
                <template x-if="files.length">
                    <tk:upload.preview
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'preview:')->classes('size-full rounded-full border-2 border-dashed')"
                        :$size
                        image:class="object-cover"
                        variable="files[0]"
                    />
                </template>
            @else
                <template x-if="multiple() && activeFiles().length > 1">
                    <tk:progress
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'progress:')"
                        variable="aggregateProgress()"
                    />
                </template>

                @if ($variant === 'button')
                    <template x-for="(file, index) in files" :key="file.id">
                        <tk:upload.chip
                            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'tile:')"
                            :$size
                            :$multiple
                            :$color
                        />
                    </template>
                @elseif ($variant === 'list')
                    <template x-for="(file, index) in files" :key="file.id">
                        <tk:upload.list-item
                            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'tile:')"
                            :$size
                            :$multiple
                            :$color
                        />
                    </template>
                @else
                    <template x-for="(file, index) in files" :key="file.id">
                        <tk:upload.tile
                            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'tile:')"
                            :$size
                            :$multiple
                            :$variant
                            :$color
                        />
                    </template>
                @endif
            @endif
        </tk:upload.dropzone>

        <tk:upload.hint
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'hint:')"
            :$size
            :$accept
            :$maxSize
            :$maxFiles
        />

        <tk:upload.modal
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'modal:')"
            :name="$previewName"
            :$size
        />
    </div>
</tk:field.wrapper>
