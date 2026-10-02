@props([
    ...TALLKit::elementProps(),
    'as' => null,
    'label' => null,
    'labelPrepend' => null,
    'labelAppend' => null,
    'for' => null,
    'size' => null,
    'srOnly' => null,
])
@php

$hasPrependOrAppend = $labelPrepend || $labelAppend;
$tag = $as ?? ($for ? 'label' : 'span');

@endphp
@if ($slot->hasActualContent() || $label)
    @if ($hasPrependOrAppend)
        <div
            {{
                $attributes->prefixed('area:')
                    ->dataKey('label')
                    ->classes(
                        'flex items-center gap-4',
                        TALLKit::textNeutral(variant: 'strong', prefix: '[:where(&)]:'),
                        TALLKit::fontSize(size: $size, weight: true),
                        ['sr-only' => $srOnly],
                    )
            }}
        >
    @endif

    @if ($labelPrepend)
        <div {{ $attributes->prefixed('label-prepend:')->classes('me-auto') }}>
            {{ $labelPrepend }}
        </div>
    @endif

    <{{ $tag }}
        x-data="label"
        {{
            $attributes->prefixed('container:')
                ->mergeDefined([
                    'id' => $for ? $for.'-label' : null,
                    'for' => $for && $tag === 'label' ? $for : null,
                ])
                ->dataKey($hasPrependOrAppend ? null : 'label')
                ->classes([
                    'cursor-default inline-flex',
                    'flex-1' => $hasPrependOrAppend,
                    'sr-only' => $srOnly && ! $hasPrependOrAppend,
                ])
        }}
    >
        <tk:element
            :$label
            :icon:size="TALLKit::adjustSize(size: $size)"
            :icon-trailing:size="TALLKit::adjustSize(size: $size)"
            :badge:size="TALLKit::adjustSize(size: $size)"
            :attributes="TALLKit::attributesMerge(
                    TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->whereDoesntStartWith(['area:', 'label-prepend:', 'label-append:', 'container:', 'info:']),
                    $attributes->prefixed('info:', keepPrefix: 'icon-trailing:'),
                )
                ->classes(
                    TALLKit::textNeutral(variant: 'strong', prefix: '[:where(&)]:'),
                    TALLKit::fontSize(size: $size, weight: true)
                )
            "
        >
            {{ $slot }}
        </tk:element>
    </{{ $tag }}>

    @if ($labelAppend)
        <div {{ $attributes->prefixed('label-append:')->classes('ms-auto') }}>
            {{ $labelAppend }}
        </div>
    @endif

    @if ($hasPrependOrAppend)
        </div>
    @endif
@endif
