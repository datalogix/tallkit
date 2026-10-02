@props([
    'size' => null,
    'variable' => 'file',
    'showError' => true,
])
@php

$fallbackTypes = ['doc', 'xls', 'ppt', 'archive', 'text', 'csv', 'code'];
$knownTypes = ['image', 'video', 'audio', 'pdf', ...$fallbackTypes];
$unknown = "!['".implode("', '", $knownTypes)."'].includes({$variable}.type)";

@endphp
<div
    {{
        $attributes->whereDoesntStartWith([
            'image:', 'video:', 'audio:', 'pdf:', 'file-',
            'loading:', 'loading-icon:',
            'error:', 'error-message:', 'info:', 'progress:'
        ])->classes('relative min-h-0 flex-1 overflow-hidden')
    }}
>
    <template x-if="{{ $variable }}.type === 'image' && {{ $variable }}.url">
        <img
            {{ $attributes->prefixed('image:')->classes('size-full object-contain') }}
            :src="{{ $variable }}.url"
            :alt="{{ $variable }}.name"
            @load="{{ $variable }}.previewLoaded = true"
            @@error="{{ $variable }}.previewFailed = true"
        />
    </template>

    <template x-if="{{ $variable }}.type === 'video' && {{ $variable }}.url">
        <video
            {{ $attributes->prefixed('video:')->classes('size-full') }}
            :src="{{ $variable }}.url"
            controls
            @loadeddata="{{ $variable }}.previewLoaded = true"
            @@error="{{ $variable }}.previewFailed = true"
        ></video>
    </template>

    <template x-if="{{ $variable }}.type === 'audio' && {{ $variable }}.url">
        <audio
            {{ $attributes->prefixed('audio:')->classes('size-full') }}
            :src="{{ $variable }}.url"
            controls
            @loadeddata="{{ $variable }}.previewLoaded = true"
            @@error="{{ $variable }}.previewFailed = true"
        ></audio>
    </template>

    <template x-if="{{ $variable }}.type === 'pdf' && {{ $variable }}.url">
        <iframe
            {{ $attributes->prefixed('pdf:')->classes('size-full pointer-events-none') }}
            :src="{{ $variable }}.url + '#toolbar=0'"
            @load="{{ $variable }}.previewLoaded = true"
            @@error="{{ $variable }}.previewFailed = true"
        ></iframe>
    </template>

    @foreach (['image', 'video', 'audio', 'pdf', ...$fallbackTypes, 'unknown'] as $fallbackType)
        <template x-if="{{ match (true) {
            $fallbackType === 'unknown' => $unknown,
            in_array($fallbackType, $fallbackTypes, true) => "{$variable}.type === '{$fallbackType}'",
            default => "{$variable}.type === '{$fallbackType}' && !{$variable}.url",
        } }}">
            <div
                {{
                    $attributes->prefixed('file-' . $fallbackType . ':')
                        ->classes('size-full flex items-center justify-center p-2')
                }}
            >
                <tk:icon
                    :attributes="$attributes->prefixed('file-' . $fallbackType . '-icon:')"
                    :size="TALLKit::adjustSize(size: $size, move: 1)"
                    name="{{ $fallbackType === 'unknown' ? 'ph:file' : 'ph:file-'.$fallbackType }}"
                />
            </div>
        </template>
    @endforeach

    <template x-if="['image', 'video', 'audio', 'pdf'].includes({{ $variable }}.type) && {{ $variable }}.url && !{{ $variable }}.previewLoaded && !{{ $variable }}.previewFailed && {{ $variable }}.status !== 'error'">
        <div
            {{
                $attributes->prefixed('loading:')
                    ->classes('absolute inset-0 z-10 flex items-center justify-center bg-black/10')
            }}
        >
            <tk:loading
                :attributes="$attributes->prefixed('loading-icon:')->classes('text-white')"
                :size="TALLKit::adjustSize(size: $size, move: 1)"
            />
        </div>
    </template>

    @if ($showError)
    <template x-if="{{ $variable }}.status === 'error'">
        <div
            {{
                $attributes->prefixed('error:')
                    ->classes('absolute inset-0 z-10 flex items-center justify-center bg-red-500/10')
            }}
        >
            <span
                {{
                    $attributes->prefixed('error-message:')
                        ->classes(
                            'bg-red-500 text-center text-white rounded p-2',
                            TALLKit::fontSize(size: $size)
                        )
                }}
                role="alert"
                x-text="{{ $variable }}.error"
            ></span>
        </div>
    </template>
    @endif
</div>
