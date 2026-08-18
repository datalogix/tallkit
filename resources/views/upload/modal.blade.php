@props([
    'size' => null,
])
<tk:modal
    :attributes="$attributes->whereDoesntStartWith(['button:', 'preview:'])"
    :$size
    x-on:closed="previewId = null"
>
    <x-slot:prepend>
        <tk:button
            :attributes="TALLKit::attributesAfter($attributes, 'button:')"
            :$size
            label="Open in new tab"
            @click="openFile"
        />
    </x-slot:prepend>

    <template x-if="previewFile">
        <div class="relative flex flex-col">
            <tk:upload.preview
                :attributes="TALLKit::attributesAfter($attributes, 'preview:')"
                :$size
                variable="previewFile"
            />

            <div class="flex items-center justify-between gap-2 px-1 py-2">
                <span x-text="previewFile.name"></span>
                <span x-text="formatSize(previewFile.size)"></span>
            </div>
        </div>
    </template>
</tk:modal>
