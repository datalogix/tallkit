@props([
    'size' => null,
    'color' => null,
])
<div
    {{
        $attributes->whereDoesntStartWith([
            'icon:', 'file-name:', 'error-message:', 'progress:',
            'view:', 'retry:', 'cancel:', 'remove:',
        ])->classes(
            '
                group/chip
                flex items-center max-w-full rounded-lg
                border border-zinc-300 dark:border-white/10
                bg-zinc-50 dark:bg-white/5
            ',
            TALLKit::gap(size: $size, mode: 'small'),
            TALLKit::paddingInline(size: $size, mode: 'small'),
            TALLKit::paddingBlock(size: $size, mode: 'smallest'),
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

    <span
        {{
            $attributes->prefixed('file-name:')
                ->classes(
                    'truncate',
                    TALLKit::fontSize(size: TALLKit::adjustSize(size: $size))
                )
        }}
        x-text="file.name"
        :title="sortable ? file.name + ' · ' + sortHint : file.name"
        @click="file.url && viewFile(file.id)"
    ></span>

    <template x-if="file.status === 'error'">
        <span
            {{
                $attributes->prefixed('error-message:')
                    ->classes(
                        'truncate',
                        TALLKit::text(color: 'red'),
                        TALLKit::fontSize(size: TALLKit::adjustSize(size: $size, move: -1))
                    )
            }}
            role="alert"
            x-text="file.error"
        ></span>
    </template>

    <template x-if="file.status === 'uploading'">
        <div class="w-10 shrink-0">
            <tk:progress
                :attributes="$attributes->prefixed('progress:')"
                :size="TALLKit::adjustSize(size: $size, move: -2)"
                position="none"
                color="blue"
                variable="file.progress"
            />
        </div>
    </template>

    <tk:button
        :attributes="$attributes->prefixed('view:')->merge(['tooltip' => 'View'])"
        :size="TALLKit::adjustSize(size: $size, move: -1)"
        x-show="file.url"
        variant="none"
        circle
        icon="eye"
        @click="viewFile(file.id)"
    />

    <tk:button
        :attributes="$attributes->prefixed('retry:')->merge(['tooltip' => 'Retry'])"
        :size="TALLKit::adjustSize(size: $size, move: -1)"
        x-show="canRetry(file)"
        variant="none"
        circle
        icon="refresh"
        @click="retryUpload(file.id)"
    />

    <tk:button
        :attributes="$attributes->prefixed('cancel:')->merge(['tooltip' => 'Cancel'])"
        :size="TALLKit::adjustSize(size: $size, move: -1)"
        x-show="file.status === 'uploading'"
        variant="none"
        circle
        icon="close"
        @click="cancelUpload(file.id)"
    />

    <tk:button
        :attributes="$attributes->prefixed('remove:')->merge(['tooltip' => 'Remove'])"
        :size="TALLKit::adjustSize(size: $size, move: -1)"
        x-show="file.status !== 'uploading'"
        variant="none"
        circle
        icon="trash"
        ::aria-describedby="sortable ? sortHintId : null"
        @click="removeFile(file.id)"
    />
</div>
