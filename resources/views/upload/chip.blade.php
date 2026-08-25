@props([
    'size' => null,
    'multiple' => null,
    'color' => null,
])
<div
    {{
        $attributes->whereDoesntStartWith([
            'icon:', 'file-name:', 'progress:',
            'view:', 'retry:', 'cancel:', 'remove:',
        ])->classes(
            'group/chip flex items-center max-w-full rounded-lg border border-zinc-300 dark:border-white/10 bg-zinc-50 dark:bg-white/5',
            TALLKit::gap(size: $size, mode: 'small'),
            TALLKit::paddingInline(size: $size, mode: 'small'),
            TALLKit::paddingBlock(size: $size, mode: 'smallest'),
        )
    }}
    :class="{
        'ring-2': dragOverIndex === index,
        '{{ TALLKit::uploadRing($color ?: 'blue') }}': dragOverIndex === index,
        'border-red-400 dark:border-red-500': file.status === 'error',
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

    <span
        {{
            TALLKit::attributesAfter($attributes, 'file-name:')
                ->classes(
                    'truncate',
                    TALLKit::fontSize(size: TALLKit::adjustSize($size))
                )
        }}
        x-text="file.name"
        @click="file.url && viewFile(file.id)"
    ></span>

    <template x-if="file.status === 'uploading'">
        <div class="w-10 shrink-0">
            <tk:progress
                :attributes="TALLKit::attributesAfter($attributes, 'progress:')"
                :size="TALLKit::adjustSize($size, move: -2)"
                position="none"
                variant="blue"
                variable="file.progress"
            />
        </div>
    </template>

    <tk:button
        :attributes="TALLKit::attributesAfter($attributes, 'retry:')"
        :size="TALLKit::adjustSize($size, move: -1)"
        x-show="file.status === 'error' || file.status === 'cancelled'"
        variant="none"
        circle
        icon="refresh"
        tooltip="Retry"
        @click="retryUpload(file.id)"
    />

    <tk:button
        :attributes="TALLKit::attributesAfter($attributes, 'cancel:')"
        :size="TALLKit::adjustSize($size, move: -1)"
        x-show="file.status === 'uploading'"
        variant="none"
        circle
        icon="close"
        tooltip="Cancel"
        @click="cancelUpload(file.id)"
    />

    <tk:button
        :attributes="TALLKit::attributesAfter($attributes, 'remove:')"
        :size="TALLKit::adjustSize($size, move: -1)"
        x-show="file.status !== 'uploading'"
        variant="none"
        circle
        icon="trash"
        tooltip="Remove"
        @click="removeFile(file.id)"
    />
</div>
