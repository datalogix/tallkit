@props([
    'size' => null,
    'multiple' => null,
    'color' => null,
])
<div
    {{
        $attributes->whereDoesntStartWith([
            'icon:', 'view:', 'retry:', 'cancel:', 'remove:',
            'progress:', 'file-name:', 'file-size:',
        ])->classes(
            'group/row flex items-center rounded-lg border border-zinc-300 dark:border-white/10',
            TALLKit::gap(size: $size),
            TALLKit::paddingInline(size: $size),
            TALLKit::paddingBlock(size: $size, mode: 'small'),
        )
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
    <tk:upload.type-icon
        :attributes="TALLKit::attributesAfter($attributes, 'icon:')->classes('shrink-0 text-zinc-400 dark:text-zinc-500')"
        :$size
    />

    <div class="flex min-w-0 flex-1 flex-col">
        <span
            {{
                TALLKit::attributesAfter($attributes, 'file-name:')
                    ->classes('truncate', TALLKit::fontSize(size: $size))
            }}
            x-text="file.name"
        ></span>

        <template x-if="file.status === 'uploading'">
            <tk:progress
                :attributes="TALLKit::attributesAfter($attributes, 'progress:')"
                :size="TALLKit::adjustSize($size)"
                position="none"
                variant="blue"
                variable="file.progress"
            />
        </template>

        <template x-if="file.status !== 'uploading'">
            <span
                {{
                    TALLKit::attributesAfter($attributes, 'file-size:')
                        ->classes(
                            'text-zinc-500 dark:text-zinc-400',
                            TALLKit::fontSize(size: TALLKit::adjustSize($size))
                        )
                }}
                x-text="formatSize(file.size)"
            ></span>
        </template>
    </div>

    <tk:button
        :attributes="TALLKit::attributesAfter($attributes, 'view:')"
        :size="TALLKit::adjustSize($size)"
        x-show="file.url"
        variant="none"
        icon="eye"
        tooltip="View"
        @click="viewFile(file.id)"
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
        :attributes="TALLKit::attributesAfter($attributes, 'cancel:')"
        :size="TALLKit::adjustSize($size)"
        x-show="file.status === 'uploading'"
        variant="none"
        icon="close"
        tooltip="Cancel"
        @click="cancelUpload(file.id)"
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
