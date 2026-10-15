@props([
    ...TALLKit::fieldProps(),
    'multiple' => null,
    'droppable' => null,
    'accept' => null,
    'maxSize' => null,
    'maxFiles' => null,
    'sortable' => null,
    'variant' => null,
    'hint' => null,
    'disk' => null,
    'stored' => false,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = $attributes->fieldContext(label: $label, id: $id, scope: get_defined_vars());
$wireModel = $attributes->whereStartsWith('wire:model')->first() ?: $wireModel;
$variant ??= 'dropzone';
if ($variant === 'avatar') {
    $multiple = false;
    $maxFiles = null;
}
$sortable ??= (bool) $multiple && ! in_array($variant, ['avatar', 'button']);
$disk = TALLKit::uploadDisk($disk);
$missing = '__tallkit_upload_missing__';
$boundValue = in_livewire() && $fieldName && data_get($this, $fieldName, $missing) !== $missing ? data_get($this, $fieldName) : null;

if ($boundValue !== null && $stored === false) {
    $boundValue = TALLKit::uploadServerValue($this, $fieldName, $boundValue);
}

$keptName = null;
$keptField = null;

if (! in_livewire() && $name) {
    $baseName = Str::before($name, '[]');
    $keptName = (str_ends_with($baseName, ']') ? substr($baseName, 0, -1).'_kept]' : $baseName.'_kept');
    $keptField = Str::replace(['[', ']'], ['.', ''], $keptName);

    $oldInput = request()->hasSession() ? request()->session()->getOldInput() : [];

    if (Arr::has($oldInput, $keptField)) {
        $value = array_values(array_filter(
            (array) Arr::get($oldInput, $keptField),
            fn ($file) => is_string($file) && TALLKit::uploadIsStored($file, $stored !== false ? $stored : $value, $disk),
        ));
    }
}

$files = TALLKit::uploadedFiles(value: $value ?? $boundValue, disk: $disk, stored: $stored);
$previewName = TALLKit::stableId('upload-preview');
$hintText = $hint !== false ? TALLKit::uploadHintText($accept, $maxSize, $maxFiles, (bool) $multiple) : null;

@endphp
<tk:field.wrapper
    :$name
    :attributes="$attributes->withFieldProps(get_defined_vars())"
>
    <div
        wire:ignore
        x-cloak
        x-data="upload({{ Js::from([
            'wireModel' => $wireModel,
            'multiple' => (bool) $multiple,
            'droppable' => $droppable ?? true,
            'maxSize' => $maxSize ?: null,
            'maxSizes' => collect([...array_keys(TALLKit::uploadFileTypes()), 'default'])->mapWithKeys(fn ($kind) => [$kind => TALLKit::uploadMaxSize($kind, $maxSize ? (int) $maxSize : null)]),
            'maxFiles' => $maxFiles ?: null,
            'sortable' => $sortable,
            'invalid' => (bool) $invalid,
            'files' => $files,
            'tooLargeMessage' => __('The file may not be larger than :size.'),
            'invalidTypeMessage' => __('This file type is not allowed.'),
            'tooManyFilesMessage' => __('Too many files selected.'),
            'uploadFailedMessage' => __('The file could not be uploaded.'),
            'movedMessage' => __('Moved to position :position of :total.'),
            'sortHint' => __('Drag, or press Alt and an arrow key, to move it.'),
            'sortHintId' => $id.'-sort-hint',
            'previewName' => $previewName,
            'fileTypes' => TALLKit::uploadFileTypes(),
        ]) }})"
        :data-invalid="isInvalid() || null"
        :aria-invalid="isInvalid() ? 'true' : null"
        {{
            $attributes->prefixed('control:')
                ->dataKey('control')
                ->classes('flex flex-col gap-4')
                ->merge([
                    ...TALLKit::fieldInvalid($invalid),
                ])
        }}
    >
        <input
            {{
                $attributes->withoutFieldPrefixes([
                    'dropzone:', 'progress:', 'tile:', 'hint:', 'modal:', 'preview:', 'error-message:', 'wire:model',
                ])->merge([
                    'name' => $multiple && $name && ! str_ends_with($name, '[]') ? $name.'[]' : $name,
                    'id' => $id,
                    'accept' => $accept,
                    'multiple' => $multiple,
                    ...TALLKit::fieldInvalid($invalid),
                    'aria-describedby' => TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError, hint: $hintText)
                ])
                ->classes('sr-only peer/upload-input')
            }}
            type="file"
            x-ref="fileInput"
        />

        @if ($keptName)
            <input type="hidden" name="{{ $keptName }}" value="">
            <template x-for="file in files.filter((file) => file.value !== null)" :key="file.id">
                <input type="hidden" name="{{ $keptName }}[]" :value="file.value">
            </template>
        @endif

        <tk:upload.dropzone
            :attributes="$attributes->prefixed('dropzone:')"
            :$size
            :$multiple
            :$variant
            :$color
        >
            @if ($variant === 'avatar')
                <template x-if="files.length && files[0].url">
                    <tk:upload.preview
                        :attributes="$attributes->prefixed('preview:')->classes('size-full rounded-full border-2 border-dashed')"
                        :$size
                        image:class="object-cover"
                        variable="files[0]"
                        :show-error="false"
                    />
                </template>
            @else
                <template x-if="multiple() && activeFiles().length > 1">
                    <tk:progress
                        :attributes="$attributes->prefixed('progress:')"
                        variable="aggregateProgress()"
                    />
                </template>

                @if ($variant === 'button')
                    <template x-for="(file, index) in files" :key="file.id">
                        <tk:upload.chip
                            :attributes="$attributes->prefixed('tile:')"
                            :$size
                            :$color
                        />
                    </template>
                @elseif ($variant === 'list')
                    <template x-for="(file, index) in files" :key="file.id">
                        <tk:upload.list-item
                            :attributes="$attributes->prefixed('tile:')"
                            :$size
                            :$color
                        />
                    </template>
                @else
                    <template x-for="(file, index) in files" :key="file.id">
                        <tk:upload.tile
                            :attributes="$attributes->prefixed('tile:')"
                            :$size
                            :$multiple
                            :$variant
                            :$color
                        />
                    </template>
                @endif
            @endif
        </tk:upload.dropzone>

        @if ($variant === 'avatar')
            <template x-if="files[0]?.status === 'error'">
                <span
                    {{
                        $attributes->prefixed('error-message:')
                            ->classes(
                                TALLKit::text(color: 'red'),
                                TALLKit::fontSize(size: TALLKit::adjustSize(size: $size))
                            )
                    }}
                    role="alert"
                    x-text="files[0].error"
                ></span>
            </template>
        @endif

        @if ($hintText)
            <tk:upload.hint
                :attributes="$attributes->prefixed('hint:')"
                :$size
                :$id
                :$accept
                :$maxSize
                :$maxFiles
                :$multiple
            />
        @endif

        <div class="sr-only" aria-live="polite" x-text="announcement"></div>
        @if ($sortable)
            <span id="{{ $id }}-sort-hint" class="sr-only">{{ __('Drag, or press Alt and an arrow key, to move it.') }}</span>
        @endif

        <tk:upload.modal
            :attributes="$attributes->prefixed('modal:')"
            :name="$previewName"
            :$size
        />
    </div>

    <script type="application/json" {{ TALLKit::dataKey('upload-state') }}>{!! json_encode($files, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
</tk:field.wrapper>
