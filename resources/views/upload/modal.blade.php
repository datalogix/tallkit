@props([
    'size' => null,
    'title' => null,
    'subtitle' => null,
    'description' => null,
    'append' => null,
    'actions' => null,
])
<tk:modal
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), ['title' => null, 'subtitle' => null, 'description' => null, 'append' => null, 'actions' => null])->whereDoesntStartWith(['button:', 'preview:'])"
    :$size
    x-on:closed="previewId = null"
>
    <x-slot:prepend>
        <tk:button
            :attributes="$attributes->prefixed('button:')->merge(['label' => 'Open in new tab'])"
            :$size
            @click="openFile"
        />
    </x-slot:prepend>

    <template x-if="previewFile()">
        <div class="relative flex flex-col">
            <tk:upload.preview
                :attributes="$attributes->prefixed('preview:')"
                :$size
                variable="previewFile()"
            />

            <div class="flex items-center justify-between gap-2 px-1 py-2">
                <span x-text="previewFile().name"></span>
                <span x-text="formatSize(previewFile().size)"></span>
            </div>
        </div>
    </template>
</tk:modal>
