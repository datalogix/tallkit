@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'multiple' => null,
    'variant' => null,
    'color' => null,
    'tooltip' => null,
])
@php

$label ??= match ($variant) {
    'avatar', 'gallery' => null,
    'button' => $multiple ? 'Select files' : 'Select file',
    'list' => $multiple ? 'Add files' : 'Add file',
    default => 'Drag or click to select',
};
$icon ??= $variant === 'avatar' ? 'mdi:camera-outline' : 'cloud-upload-outline';
$tooltip ??= $variant === 'avatar' ? 'Upload photo' : null;
$edgeButton = match ($size) {
    'xs', 'sm' => ['size-4! p-0!', 'size-2.5!'],
    'xl', '2xl', '3xl' => ['size-6! p-0!', 'size-3.5!'],
    default => ['size-5! p-0!', 'size-3!'],
};
$ariaLabel ??= in_array($variant, ['avatar', 'button', 'list'], true) || $label ? null : ($multiple ? 'Add files' : 'Add file');

$button = TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps(), ['tooltip' => null])
    ->whereDoesntStartWith(['container:', 'edit:', 'remove:', 'cancel:', 'progress:']);

@endphp
<div
    {{
        $attributes->prefixed('container:')
            ->dataKey('upload-dropzone')
            ->classes([
                'relative rounded-lg transition-all',
                'peer-focus-visible/upload-input:tk-focus-outline',
                'inline-flex '.TALLKit::widthHeight(size: $size, mode: 'large') => $variant === 'avatar',
                'flex flex-col gap-2 w-full' => in_array($variant, ['button', 'list']) && $multiple,
                'inline-flex max-w-full items-start flex-col gap-2' => in_array($variant, ['button', 'list']) && ! $multiple,
                'flex flex-wrap gap-4' => $multiple && ! in_array($variant, ['avatar', 'button', 'list']),
                match ($size) {
                    'xs' => 'h-40 w-40',
                    'sm' => 'h-44 w-44',
                    'lg' => 'h-52 w-52',
                    'xl' => 'h-56 w-56',
                    '2xl' => 'h-60 w-60',
                    '3xl' => 'h-64 w-64',
                    default => 'h-48 w-48',
                } => ! $multiple && ! in_array($variant, ['avatar', 'button', 'list']),
                'rounded-full' => $variant === 'avatar',
            ])
    }}
    @if ($variant === 'avatar')
        :class="{
            'ring-2': dragOver,
            '{{ TALLKit::ring(color: $color ?: 'blue') }}': dragOver,
            '{{ TALLKit::faintBackground(color: $color ?: 'blue') }}': dragOver,
        }"
    @endif
>
    @if ($variant === 'avatar')
        <tk:button
            x-show="files.length === 0"
            :attributes="$button->classes('size-full rounded-full border-2 border-dashed')"
            :$size
            tabindex="-1"
            @click="selectFile"
        />

        {{ $slot }}

        <tk:button
            x-show="files.length > 0"
            :attributes="$attributes->prefixed('edit:')->merge(['icon' => 'pencil', 'tooltip' => 'Change photo'])->classes('absolute inset-0 size-full! p-0! shadow')"
            :size="TALLKit::adjustSize(size: $size)"
            variant="filled"
            circle
            tabindex="-1"
            @click="selectFile"
        />

        <div
            {{
                $attributes->prefixed('progress:')
                    ->classes(
                        'pointer-events-none absolute inset-0 flex items-center justify-center rounded-full bg-black/50 text-white tabular-nums',
                        TALLKit::fontSize(size: TALLKit::adjustSize(size: $size))
                    )
                    ->merge(['aria-label' => __('Upload')])
            }}
            x-show="files[0]?.status === 'uploading'"
            role="progressbar"
            aria-valuemin="0"
            aria-valuemax="100"
            :aria-valuenow="files[0]?.progress ?? 0"
            x-text="(files[0]?.progress ?? 0) + '%'"
        ></div>

        <tk:button
            x-show="files[0]?.status === 'uploading'"
            :attributes="$attributes->prefixed('cancel:')->merge(['icon' => 'close', 'tooltip' => 'Cancel'])->classes('absolute z-10 shadow top-[14.6%] end-[14.6%] -translate-y-1/2 translate-x-1/2 rtl:-translate-x-1/2', $edgeButton[0])"
            size="xs"
            :icon:class="$edgeButton[1]"
            variant="filled"
            circle
            @click="cancelUpload(files[0].id)"
        />

        <tk:button
            x-show="files.length > 0 && files[0].status !== 'uploading'"
            :attributes="$attributes->prefixed('remove:')->merge(['icon' => 'trash', 'tooltip' => 'Remove photo'])->classes('absolute z-10 shadow top-[14.6%] end-[14.6%] -translate-y-1/2 translate-x-1/2 rtl:-translate-x-1/2', $edgeButton[0])"
            size="xs"
            :icon:class="$edgeButton[1]"
            variant="filled"
            circle
            @click="removeFile(files[0].id)"
        />
    @elseif ($variant === 'button')
        <tk:button
            :attributes="$button"
            :$size
            tabindex="-1"
            ::class="{
                'ring-2': dragOver && dragOverIndex === null,
                '{{ TALLKit::ring(color: $color ?: 'blue') }}': dragOver && dragOverIndex === null,
            }"
            @click="selectFile"
        />

        {{ $slot }}
    @elseif ($variant === 'list')
        <tk:button
            :attributes="$button->classes('justify-start border border-dashed')"
            :$size
            tabindex="-1"
            ::class="{
                'ring-2': dragOver && dragOverIndex === null,
                '{{ TALLKit::ring(color: $color ?: 'blue') }}': dragOver && dragOverIndex === null,
                '{{ TALLKit::ringBorder(color: $color ?: 'blue') }}': dragOver && dragOverIndex === null,
            }"
            variant="outline"
            @click="selectFile"
        />

        {{ $slot }}
    @else
        <tk:button
            x-show="multiple() || files.length === 0"
            :attributes="$button
                ->classes([
                    'flex-col border-2 border-dashed whitespace-normal',
                    'w-full' => $multiple,
                    'size-full' => ! $multiple,
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
            tabindex="-1"
            ::class="{
                'ring-2': dragOver && dragOverIndex === null,
                '{{ TALLKit::ring(color: $color ?: 'blue') }}': dragOver && dragOverIndex === null,
                '{{ TALLKit::faintBackground(color: $color ?: 'blue') }}': dragOver && dragOverIndex === null,
                '{{ TALLKit::ringBorder(color: $color ?: 'blue') }}': dragOver && dragOverIndex === null,
                '{{ TALLKit::text(color: $color ?: 'blue') }}': dragOver && dragOverIndex === null,
            }"
            :icon:size="TALLKit::adjustSize(size: $size, move: 1)"
            @click="selectFile"
        />

        {{ $slot }}
    @endif
</div>
