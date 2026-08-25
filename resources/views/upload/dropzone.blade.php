@props([
    'size' => null,
    'multiple' => null,
    'variant' => null,
    'color' => 'blue',
])
<div
    {{
        TALLKit::attributesAfter($attributes, 'container:')
            ->dataKey('upload-dropzone')
            ->classes([
                'relative rounded-lg transition-all',
                'inline-flex '.TALLKit::widthHeight(size: $size, mode: 'large') => $variant === 'avatar',
                'flex flex-col gap-2 w-full' => in_array($variant, ['button', 'list']) && $multiple,
                'inline-flex items-start flex-col gap-2' => in_array($variant, ['button', 'list']) && ! $multiple,
                'flex flex-wrap gap-4' => $multiple && ! in_array($variant, ['avatar', 'button', 'list']),
                match ($size) {
                    'xs' => 'h-40 w-40',
                    'sm' => 'h-44 w-44',
                    'lg' => 'h-52 w-52',
                    'xl' => 'h-56 w-56',
                    '2xl' => 'h-60 w-60',
                    '3xl' => 'h-64 w-64',
                    default => 'h-48 w-48',
                } => !$multiple && ! in_array($variant, ['avatar', 'button', 'list']),
                'rounded-full' => $variant === 'avatar',
            ])
    }}
    @if ($variant === 'avatar')
        :class="{
            'ring-2': dragOver,
            '{{ TALLKit::uploadRing($color) }}': dragOver,
            '{{ TALLKit::uploadBg($color) }}': dragOver,
        }"
    @endif
>
    @if ($variant === 'avatar')
        <tk:button
            x-show="files.length === 0"
            :attributes="$attributes->whereDoesntStartWith(['container:'])->classes('size-full rounded-full border-2 border-dashed')"
            :$size
            icon="mdi:camera-outline"
            tooltip="Upload photo"
            @click="selectFile"
        />

        {{ $slot }}

        <tk:button
            x-show="files.length > 0"
            :attributes="TALLKit::attributesAfter($attributes, 'edit:')->classes('absolute inset-0 p-0! shadow')"
            :size="TALLKit::adjustSize($size)"
            variant="filled"
            circle
            icon="pencil"
            tooltip="Change photo"
            @click="selectFile"
        />
    @elseif ($variant === 'button')
        <tk:button
            :attributes="$attributes->whereDoesntStartWith(['container:'])"
            :$size
            ::class="{
                'ring-2': dragOver && dragOverIndex === null,
                '{{ TALLKit::uploadRing($color) }}': dragOver && dragOverIndex === null,
            }"
            :label="$multiple ? 'Select files' : 'Select file'"
            icon="cloud-upload-outline"
            @click="selectFile"
        />

        {{ $slot }}
    @elseif ($variant === 'list')
        <tk:button
            :attributes="$attributes->whereDoesntStartWith(['container:'])->classes('justify-start border border-dashed')"
            :$size
            ::class="{
                'ring-2': dragOver && dragOverIndex === null,
                '{{ TALLKit::uploadRing($color) }}': dragOver && dragOverIndex === null,
                '{{ TALLKit::uploadBorder($color) }}': dragOver && dragOverIndex === null,
            }"
            :label="$multiple ? 'Add files' : 'Add file'"
            icon="cloud-upload-outline"
            variant="outline"
            @click="selectFile"
        />

        {{ $slot }}
    @else
        <tk:button
            x-show="multiple || files.length === 0"
            :attributes="$attributes->whereDoesntStartWith(['container:'])
                ->classes([
                    'flex-col border-2 border-dashed whitespace-normal',
                    'w-full' => $multiple,
                    'size-full' => !$multiple,
                    match ($size) {
                        'xs' => 'h-24',
                        'sm' => 'h-28',
                        'lg' => 'h-36',
                        'xl' => 'h-40',
                        '2xl' => 'h-44',
                        '3xl' => 'h-48',
                        default => 'h-32',
                    } => $multiple,
                ])
            "
            :$size
            ::class="{
                'ring-2': dragOver && dragOverIndex === null,
                '{{ TALLKit::uploadRing($color) }}': dragOver && dragOverIndex === null,
                '{{ TALLKit::uploadBg($color) }}': dragOver && dragOverIndex === null,
                '{{ TALLKit::uploadBorder($color) }}': dragOver && dragOverIndex === null,
                '{{ TALLKit::uploadText($color) }}': dragOver && dragOverIndex === null,
            }"
            :label="$variant === 'gallery' ? null : 'Drag or click to select'"
            icon="cloud-upload-outline"
            :icon:size="TALLKit::adjustSize($size, move: 1)"
            @click="selectFile"
        />

        {{ $slot }}
    @endif
</div>
