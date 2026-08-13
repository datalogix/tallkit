@props([
    'rootAttributes' => null,
])
<div
    data-tallkit-upload-dropzone
    class="relative rounded-lg transition-all"
    :class="multiple ? 'flex flex-wrap gap-4' : 'h-48 w-48 overflow-hidden'"
    ::class="{ 'ring-2 ring-blue-700 dark:ring-blue-300': dragOver }"
>
    <tk:button
        x-show="multiple || files.length === 0"
        :attributes="TALLKit::attributesAfter($rootAttributes, 'button:')"
        class="flex-col border-2 border-dashed whitespace-normal"
        ::class="multiple ? 'w-full h-32' : 'size-full'"
        label="{{ __('Drag or click to select') }}"
        icon="cloud-upload-outline"
        icon:size="xl"
        @click="selectFile"
    />

    <template x-for="(file, index) in files" :key="file.id">
        <tk:upload.tile :rootAttributes="$rootAttributes" />
    </template>
</div>
