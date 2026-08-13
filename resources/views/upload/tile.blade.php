@props([
    'rootAttributes' => null,
])
<div
    class="relative flex flex-col rounded-lg overflow-hidden border border-zinc-300 dark:border-white/10 transition-all duration-200"
    :class="multiple ? 'h-48 w-54' : 'size-full'"
    :draggable="sortable"
    @dragstart="dragStart(index, $event)"
    @dragover.prevent
    @drop.prevent="drop(index)"
    @dragend="dragEnd"
>
    <template x-if="file.type === 'image'">
        <img :src="file.url" class="size-full object-cover" />
    </template>

    <template x-if="file.type === 'video'">
        <video :src="file.url" controls class="size-full"></video>
    </template>

    <template x-if="file.type === 'audio'">
        <audio :src="file.url" controls class="h-32 w-full"></audio>
    </template>

    <template x-if="file.type === 'pdf'">
        <iframe :src="file.url + '#toolbar=0'" class="size-full pointer-events-none"></iframe>
    </template>

    @foreach (['doc', 'xls', 'ppt', 'archive', 'text', 'csv', 'code', 'unknown'] as $fallbackType)
        <template x-if="file.type === '{{ $fallbackType }}'">
            <div class="size-full flex items-center justify-center p-2">
                <tk:icon name="ph:file-{{ $fallbackType }}" size="xl" />
            </div>
        </template>
    @endforeach

    <div
        x-show="file.status === 'error'"
        class="absolute inset-0 z-10 flex items-center justify-center bg-red-500/10 p-2"
    >
        <tk:text x-text="file.error" class="text-center" variant="red" size="sm" />
    </div>

    <div class="absolute inset-x-0 bottom-0 flex flex-col">
        <div
            x-show="file.status === 'uploading'"
            class="h-1 bg-black/10 dark:bg-white/20"
        >
            <div
                class="h-full bg-blue-700 dark:bg-blue-300 transition-[width] duration-150"
                :style="{ width: file.progress + '%' }"
            ></div>
        </div>

        <div class="flex items-center justify-between gap-2 bg-black/50 px-2 py-1">
            <tk:text x-text="file.name" class="flex-1 truncate text-white" size="xs" />
            <tk:text x-text="formatSize(file.size)" class="shrink-0 text-white/70" size="xs" />
        </div>
    </div>

    <div class="absolute top-1 right-1 flex gap-1">
        <tk:button
            x-show="file.url"
            :attributes="TALLKit::attributesAfter($rootAttributes, 'view:')"
            variant="none"
            size="xs"
            icon="eye"
            tooltip="{{ __('View') }}"
            @click="window.open(file.url, '_blank', 'noopener')"
        />

        <tk:button
            x-show="!multiple && file.status === 'done'"
            :attributes="TALLKit::attributesAfter($rootAttributes, 'edit:')"
            variant="none"
            size="xs"
            icon="pencil"
            tooltip="{{ __('Edit') }}"
            @click="selectFile"
        />

        <tk:button
            x-show="file.status === 'uploading'"
            :attributes="TALLKit::attributesAfter($rootAttributes, 'cancel:')"
            variant="none"
            size="xs"
            icon="close"
            tooltip="{{ __('Cancel') }}"
            @click="cancelUpload(file.id)"
        />

        <tk:button
            x-show="file.status === 'error' || file.status === 'cancelled'"
            :attributes="TALLKit::attributesAfter($rootAttributes, 'retry:')"
            variant="none"
            size="xs"
            icon="refresh"
            tooltip="{{ __('Retry') }}"
            @click="retryUpload(file.id)"
        />

        <tk:button
            :attributes="TALLKit::attributesAfter($rootAttributes, 'remove:')"
            variant="none"
            size="xs"
            icon="trash"
            tooltip="{{ __('Remove') }}"
            @click="removeFile(file.id)"
        />
    </div>
</div>
