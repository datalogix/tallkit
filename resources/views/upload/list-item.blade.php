@props([
    'size' => null,
    'color' => null,
])
<div
    {{
        $attributes->whereDoesntStartWith([
            'icon:', 'view:', 'retry:', 'cancel:', 'remove:',
            'progress:', 'file-name:', 'file-size:', 'error-message:',
        ])->classes(
            '
                group/row
                flex max-w-full items-center rounded-lg
                border border-zinc-300 dark:border-white/10',
            TALLKit::gap(size: $size),
            TALLKit::paddingInline(size: $size),
            TALLKit::paddingBlock(size: $size, mode: 'small'),
        )
    }}
    :class="{
        'ring-2': dragOverIndex === index,
        '{{ TALLKit::ring(color: $color ?: 'blue') }}': dragOverIndex === index,
        'border-red-400 dark:border-red-500': file.status === 'error',
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
    <tk:upload.type-icon
        :attributes="$attributes->prefixed('icon:')->classes('shrink-0', TALLKit::textNeutral(variant: 'muted'))"
        :$size
    />

    <div class="flex min-w-0 flex-1 flex-col">
        <span
            {{
                $attributes->prefixed('file-name:')
                    ->classes('truncate', TALLKit::fontSize(size: $size))
            }}
            x-text="file.name"
            :title="sortable ? file.name + ' · ' + sortHint : file.name"
        ></span>

        <template x-if="file.status === 'uploading'">
            <tk:progress
                :attributes="$attributes->prefixed('progress:')"
                :size="TALLKit::adjustSize(size: $size)"
                position="none"
                color="blue"
                variable="file.progress"
            />
        </template>

        <template x-if="file.status === 'error'">
            <span
                {{
                    $attributes->prefixed('error-message:')
                        ->classes(
                            'truncate',
                            TALLKit::text(color: 'red'),
                            TALLKit::fontSize(size: TALLKit::adjustSize(size: $size))
                        )
                }}
                role="alert"
                x-text="file.error"
            ></span>
        </template>

        <template x-if="file.status !== 'uploading' && file.status !== 'error'">
            <span
                {{
                    $attributes->prefixed('file-size:')
                        ->classes(
                            TALLKit::textNeutral(variant: 'subtle'),
                            TALLKit::fontSize(size: TALLKit::adjustSize(size: $size))
                        )
                }}
                x-text="formatSize(file.size)"
            ></span>
        </template>
    </div>

    <tk:button
        :attributes="$attributes->prefixed('view:')->merge(['tooltip' => 'View'])"
        :size="TALLKit::adjustSize(size: $size)"
        x-show="file.url"
        variant="none"
        icon="eye"
        @click="viewFile(file.id)"
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
        :attributes="$attributes->prefixed('cancel:')->merge(['tooltip' => 'Cancel'])"
        :size="TALLKit::adjustSize(size: $size)"
        x-show="file.status === 'uploading'"
        variant="none"
        icon="close"
        @click="cancelUpload(file.id)"
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
