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
            'bg-zinc-100 dark:bg-white/5' => $variant === 'gallery',
            'size-full' => ! $multiple,
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
        '{{ TALLKit::ring(color: $color ?: 'blue') }}': dragOverIndex === index,
    }"
    :draggable="sortable"
    @dragstart="dragStart(index, $event)"
    @dragover.prevent="dragOverTile(index)"
    @dragleave.prevent="dragLeaveTile(index, $event)"
    @drop.prevent.stop="dropOnTile(index, $event)"
    @dragend="dragEnd"
    @keydown.alt.up.prevent="moveByKey(index, 'up')"
    @keydown.alt.down.prevent="moveByKey(index, 'down')"
    @keydown.alt.left.prevent="moveByKey(index, 'left')"
    @keydown.alt.right.prevent="moveByKey(index, 'right')"
>
    <div
        {{
            $attributes->prefixed('actions:')
                ->classes([
                    '
                        flex items-center justify-end gap-1 bg-black/50 px-2 py-1

                        **:[:where(&)]:text-white/80
                        **:[:where(&)]:hover:text-white
                        **:[:where(&)]:[&[data-active]]:text-white
                    ',
                    '
                        absolute inset-x-0 top-0 z-10
                        opacity-0 transition-opacity
                        group-hover/tile:opacity-100
                        group-focus-within/tile:opacity-100
                        pointer-coarse:opacity-100
                    ' => $variant === 'gallery',
                ])
        }}
    >
        <tk:button
            :attributes="$attributes->prefixed('view:')->merge(['tooltip' => 'View'])"
            :size="TALLKit::adjustSize(size: $size)"
            x-show="file.url"
            variant="none"
            icon="eye"
            @click="$event.currentTarget.blur(); viewFile(file.id)"
        />

        <tk:button
            :attributes="$attributes->prefixed('edit:')->merge(['tooltip' => 'Edit'])"
            :size="TALLKit::adjustSize(size: $size)"
            x-show="!multiple() && file.status === 'done'"
            variant="none"
            icon="pencil"
            @click="selectFile"
        />

        <tk:button
            :attributes="$attributes->prefixed('cancel:')->merge(['tooltip' => 'Cancel'])"
            :size="TALLKit::adjustSize(size: $size)"
            x-show="file.status === 'uploading'"
            variant="none"
            icon="close"
            @click="cancelUpload(file.id)"
        />

        <tk:button
            :attributes="$attributes->prefixed('retry:')->merge(['tooltip' => 'Retry'])"
            :size="TALLKit::adjustSize(size: $size)"
            x-show="canRetry(file)"
            variant="none"
            icon="refresh"
            @click="retryUpload(file.id)"
        />

        <tk:button
            :attributes="$attributes->prefixed('remove:')->merge(['tooltip' => 'Remove'])"
            :size="TALLKit::adjustSize(size: $size)"
            x-show="file.status !== 'uploading'"
            variant="none"
            icon="trash"
            ::aria-describedby="sortable ? sortHintId : null"
            @click="removeFile(file.id)"
        />
    </div>

    <tk:upload.preview
        :attributes="$attributes->prefixed('preview:')"
        :$size
    />

    <div
        {{
            $attributes->prefixed('info:')
                ->classes([
                    'flex flex-col',
                    'absolute inset-x-0 bottom-0 z-10' => $variant === 'gallery',
                ])
        }}
    >
        <tk:progress
            x-show="file.status === 'uploading'"
            :attributes="$attributes->prefixed('progress:')"
            :$size
            position="none"
            color="blue"
            variable="file.progress"
            square
        />

        <div
            {{
                $attributes->prefixed('file-info:')
                    ->classes([
                        'flex items-center justify-between gap-2 bg-black/50 px-2 py-1',
                        '
                            opacity-0 transition-opacity
                            group-hover/tile:opacity-100
                            group-focus-within/tile:opacity-100
                            pointer-coarse:opacity-100
                        ' => $variant === 'gallery',
                    ])
            }}
        >
            <span
                {{
                    $attributes->prefixed('file-name:')
                        ->classes(
                            'flex-1 truncate text-white',
                            TALLKit::fontSize(size: TALLKit::adjustSize(size: $size))
                        )
                }}
                x-text="file.name"
                :title="sortable ? file.name + ' · ' + sortHint : file.name"
            ></span>
            <span
                {{
                    $attributes->prefixed('file-size:')
                        ->classes(
                            'shrink-0 text-white/70',
                            TALLKit::fontSize(size: TALLKit::adjustSize(size: $size))
                        )
                }}
                x-text="formatSize(file.size)"
            ></span>
        </div>
    </div>
</div>
