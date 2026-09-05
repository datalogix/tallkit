@props([
    'size' => null,
    'variable' => 'file',
])
<div
    {{
        $attributes->whereDoesntStartWith([
            'image:', 'video:', 'audio:', 'pdf:', 'file-',
            'loading:', 'loading-icon:',
            'error:', 'error-message:', 'info:', 'progress:'
        ])->classes('relative min-h-0 flex-1 overflow-hidden')
    }}
>
    <template x-if="{{ $variable }}.type === 'image'">
        <img
            {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'image:')->classes('size-full object-contain') }}
            :src="{{ $variable }}.url"
            @load="{{ $variable }}.previewLoaded = true"
            @@error="{{ $variable }}.previewLoaded = true"
        />
    </template>

    <template x-if="{{ $variable }}.type === 'video'">
        <video
            {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'video:')->classes('size-full') }}
            :src="{{ $variable }}.url"
            controls
            @loadeddata="{{ $variable }}.previewLoaded = true"
            @@error="{{ $variable }}.previewLoaded = true"
        ></video>
    </template>

    <template x-if="{{ $variable }}.type === 'audio'">
        <audio
            {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'audio:')->classes('size-full') }}
            :src="{{ $variable }}.url"
            controls
            @loadeddata="{{ $variable }}.previewLoaded = true"
            @@error="{{ $variable }}.previewLoaded = true"
        ></audio>
    </template>

    <template x-if="{{ $variable }}.type === 'pdf'">
        <iframe
            {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'pdf:')->classes('size-full pointer-events-none') }}
            :src="{{ $variable }}.url + '#toolbar=0'"
            @load="{{ $variable }}.previewLoaded = true"
            @@error="{{ $variable }}.previewLoaded = true"
        ></iframe>
    </template>

    @foreach (['doc', 'xls', 'ppt', 'archive', 'text', 'csv', 'code', 'unknown'] as $fallbackType)
        <template x-if="{{ $variable }}.type === '{{ $fallbackType }}'">
            <div
                {{
                    TALLKit::attributesAfter(attributes: $attributes, prefix: 'file-' . $fallbackType . ':')
                        ->classes('size-full flex items-center justify-center p-2')
                }}
            >
                <tk:icon
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'file-' . $fallbackType . '-icon:')"
                    :size="TALLKit::adjustSize(size: $size, move: 1)"
                    name="ph:file-{{ $fallbackType }}"
                />
            </div>
        </template>
    @endforeach

    <div
        {{
            TALLKit::attributesAfter(attributes: $attributes, prefix: 'loading:')
                ->classes('absolute inset-0 z-10 flex items-center justify-center bg-black/10')
        }}
        x-show="{{ $variable }}.url && !{{ $variable }}.previewLoaded && {{ $variable }}.status !== 'error'"
    >
        <tk:loading
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'loading-icon:')->classes('text-white')"
            :size="TALLKit::adjustSize(size: $size, move: 1)"
        />
    </div>

    <div
        {{
            TALLKit::attributesAfter(attributes: $attributes, prefix: 'error:')
                ->classes('absolute inset-0 z-10 flex items-center justify-center bg-red-500/10')
        }}
        x-show="{{ $variable }}.status === 'error'"
    >
        <span
            {{
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'error-message:')
                    ->classes(
                        'bg-red-500 text-center text-white rounded p-2',
                        TALLKit::fontSize(size: $size)
                    )
            }}
            x-text="{{ $variable }}.error"
        ></span>
    </div>
</div>
