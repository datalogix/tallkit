@props([
    'as' => null,
    'label' => null,
    'label-prepend' => null,
    'label-append' => null,
    'for' => null,
    'size' => null,
    'srOnly' => null,
])
@php

$labelPrepend = ${'label-prepend'} ?? $attributes->pluck('labelPrepend');
$labelAppend = ${'label-append'} ?? $attributes->pluck('labelAppend');
$hasPrependOrAppend = $labelPrepend || $labelAppend;

@endphp
@if ($slot->hasActualContent() || $label)
    @if ($hasPrependOrAppend)
        <div
            {{
                TALLKit::attributesAfter($attributes, 'area:')
                    ->dataKey('label')
                    ->classes(
                        'flex items-center gap-4',
                        '[:where(&)]:text-zinc-800 dark:[:where(&)]:text-white',
                        TALLKit::fontSize(size: $size, weight: true),
                    )
            }}
        >
    @endif

    @if ($labelPrepend)
        <div {{ TALLKit::attributesAfter($attributes, 'label-prepend:')->classes('mr-auto') }}>
            {{ $labelPrepend }}
        </div>
    @endif

    <{{ $as ?? ($for ? 'label' : 'span') }}
        x-data="label"
        {{
            TALLKit::attributesAfter($attributes, 'container:')
                ->dataKey($hasPrependOrAppend ? null : 'label')
                ->classes([
                    'cursor-default inline-flex',
                    'flex-1' => $hasPrependOrAppend,
                    'sr-only' => $srOnly
                ])
        }}
        @if ($for) for="{{ $for }}" @endif
    >
        <tk:element
            :$label
            :icon:size="TALLKit::adjustSize(size: $size)"
            :icon-trailing:size="TALLKit::adjustSize(size: $size)"
            :badge:size="TALLKit::adjustSize(size: $size)"
            :attributes="$attributes->whereDoesntStartWith(['area:', 'label-prepend:', 'label-append:', 'container:', 'info:'])
                ->merge(TALLKit::attributesAfter($attributes, 'info:', prepend: 'icon-trailing:')->getAttributes())
                ->classes(
                    '[:where(&)]:text-zinc-800 dark:[:where(&)]:text-white',
                    TALLKit::fontSize(size: $size, weight: true)
                )
            "
        >
            {{ $slot }}
        </tk:element>
    </{{ $as ?? ($for ? 'label' : 'span') }}>

    @if ($labelAppend)
        <div {{ TALLKit::attributesAfter($attributes, 'label-append:')->classes('ml-auto') }}>
            {{ $labelAppend }}
        </div>
    @endif

    @if ($hasPrependOrAppend)
        </div>
    @endif
@endif
