@props([
    'size' => null,
    'multiple' => null,
])
<div
    {{
        TALLKit::attributesAfter($attributes, 'container:')
            ->dataKey('upload-dropzone')
            ->classes([
                'relative rounded-lg transition-all',
                'flex flex-wrap gap-4' => $multiple,
                'overflow-hidden' => !$multiple,
                match ($size) {
                    'xs' => 'h-40 w-40',
                    'sm' => 'h-44 w-44',
                    'lg' => 'h-52 w-52',
                    'xl' => 'h-56 w-56',
                    '2xl' => 'h-60 w-60',
                    '3xl' => 'h-64 w-64',
                    default => 'h-48 w-48',
                } => !$multiple,
            ])
    }}
    :class="{ 'ring-2 ring-blue-700 dark:ring-blue-300 bg-blue-500/10 dark:bg-blue-300/10': !multiple && dragOver }"
>
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
        ::class="{ 'ring-2 ring-blue-700 dark:ring-blue-300 bg-blue-500/10 dark:bg-blue-300/10 border-blue-700 text-blue-700 dark:border-blue-300 dark:text-blue-300': dragOver && dragOverIndex === null }"
        label="Drag or click to select"
        icon="cloud-upload-outline"
        :icon:size="TALLKit::adjustSize($size, move: 1)"
        @click="selectFile"
    />

    {{ $slot }}
</div>
