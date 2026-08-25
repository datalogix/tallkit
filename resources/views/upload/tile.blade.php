@props([
    'size' => null,
    'multiple' => null,
    'variant' => null,
    'color' => null,
])
<div
    {{
        $attributes->whereDoesntStartWith([
            'actions:',
            'view:', 'edit:', 'cancel:', 'retry:', 'remove:',
            'preview:', 'info:', 'progress:',
            'file-name:', 'file-info:', 'file-size:',
        ])->classes([
            '
                group/tile relative flex flex-col rounded-lg overflow-hidden
                transition-all duration-200
            ',
            'border border-zinc-300 dark:border-white/10' => $variant !== 'gallery',
            'size-full' => !$multiple,
            match ($variant) {
                'gallery' => match ($size) {
                    'xs' => 'h-44 w-44',
                    'sm' => 'h-48 w-48',
                    'lg' => 'h-56 w-56',
                    'xl' => 'h-60 w-60',
                    '2xl' => 'h-64 w-64',
                    '3xl' => 'h-68 w-68',
                    default => 'h-52 w-52',
                },
                default => match ($size) {
                    'xs' => 'h-40 w-46',
                    'sm' => 'h-44 w-50',
                    'lg' => 'h-52 w-58',
                    'xl' => 'h-56 w-62',
                    '2xl' => 'h-60 w-66',
                    '3xl' => 'h-64 w-70',
                    default => 'h-48 w-54',
                },
            } => $multiple,
        ])
    }}
    :class="{
        'ring-2': dragOverIndex === index,
        '{{ TALLKit::uploadRing($color ?: 'blue') }}': dragOverIndex === index,
    }"
    :draggable="sortable"
    @dragstart="dragStart(index, $event)"
    @dragover.prevent="dragOverTile(index)"
    @dragleave.prevent="dragLeaveTile(index, $event)"
    @drop.prevent.stop="dropOnTile(index, $event)"
    @dragend="dragEnd"
>
    <div
        {{
            TALLKit::attributesAfter($attributes, 'actions:')
                ->classes([
                    'flex items-center justify-end gap-1 bg-black/50 px-2 py-1',
                    'absolute inset-x-0 top-0 z-10 opacity-0 transition-opacity group-hover/tile:opacity-100 group-focus-within/tile:opacity-100' => $variant === 'gallery',
                ])
        }}
    >
        <tk:button
            :attributes="TALLKit::attributesAfter($attributes, 'view:')"
            :size="TALLKit::adjustSize($size)"
            x-show="file.url"
            variant="none"
            icon="eye"
            tooltip="View"
            @click="$event.currentTarget.blur(); viewFile(file.id)"
        />

        <tk:button
            :attributes="TALLKit::attributesAfter($attributes, 'edit:')"
            :size="TALLKit::adjustSize($size)"
            x-show="!multiple && file.status === 'done'"
            variant="none"
            icon="pencil"
            tooltip="Edit"
            @click="selectFile"
        />

        <tk:button
            :attributes="TALLKit::attributesAfter($attributes, 'cancel:')"
            :size="TALLKit::adjustSize($size)"
            x-show="file.status === 'uploading'"
            variant="none"
            icon="close"
            tooltip="Cancel"
            @click="cancelUpload(file.id)"
        />

        <tk:button
            :attributes="TALLKit::attributesAfter($attributes, 'retry:')"
            :size="TALLKit::adjustSize($size)"
            x-show="file.status === 'error' || file.status === 'cancelled'"
            variant="none"
            icon="refresh"
            tooltip="Retry"
            @click="retryUpload(file.id)"
        />

        <tk:button
            :attributes="TALLKit::attributesAfter($attributes, 'remove:')"
            :size="TALLKit::adjustSize($size)"
            variant="none"
            icon="trash"
            tooltip="Remove"
            @click="removeFile(file.id)"
        />
    </div>

    <tk:upload.preview
        :attributes="TALLKit::attributesAfter($attributes, 'preview:')"
        :$size
    />

    <div
        {{
            TALLKit::attributesAfter($attributes, 'info:')
                ->classes([
                    'flex flex-col',
                    'absolute inset-x-0 bottom-0 z-10' => $variant === 'gallery',
                ])
        }}
    >
        <tk:progress
            x-show="file.status === 'uploading'"
            :attributes="TALLKit::attributesAfter($attributes, 'progress:')->classes('rounded-none')"
            :$size
            position="none"
            variant="blue"
            variable="file.progress"
            bar:class="rounded-none"
        />

        <div
            {{
                TALLKit::attributesAfter($attributes, 'file-info:')
                    ->classes([
                        'flex items-center justify-between gap-2 bg-black/50 px-2 py-1',
                        'opacity-0 transition-opacity group-hover/tile:opacity-100 group-focus-within/tile:opacity-100' => $variant === 'gallery',
                    ])
            }}
        >
            <span
                {{
                    TALLKit::attributesAfter($attributes, 'file-name:')
                        ->classes(
                            'flex-1 truncate text-white',
                            TALLKit::fontSize(size: TALLKit::adjustSize($size))
                        )
                }}
                x-text="file.name"
            ></span>
            <span
                {{
                    TALLKit::attributesAfter($attributes, 'file-size:')
                        ->classes(
                            'shrink-0 text-white/70',
                            TALLKit::fontSize(size: TALLKit::adjustSize($size))
                        )
                }}
                x-text="formatSize(file.size)"
            ></span>
        </div>
    </div>
</div>
