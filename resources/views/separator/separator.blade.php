@props([
    'vertical' => null,
    'text' => null,
])
@php

$vertical = (bool) $vertical;

$contentClasses = TALLKit::classes(
    TALLKit::backgroundNeutral(variant: 'strong'),
    'border-0 [print-color-adjust:exact]',
    $vertical ? 'self-stretch self-center w-px h-full' : 'h-px w-full',
);

@endphp
@if ($slot->hasActualContent() || $text)
    <div
        role="separator"
        @if ($vertical) aria-orientation="vertical" @endif
        {{ TALLKit::dataKey(name: 'separator') }}
        class="flex items-center w-full"
    >
        <div {{ $attributes->whereDoesntStartWith(['content:'])->classes($contentClasses->add('grow')) }}></div>

        <span {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'content:')->classes(
            'shrink mx-6 whitespace-nowrap',
            TALLKit::textNeutral(variant: 'subtle'),
            TALLKit::fontSize(weight: true),
        ) }}>
            {{ $slot->isEmpty() ? __($text) : $slot }}
        </span>

        <div {{ $attributes->whereDoesntStartWith(['content:'])->classes($contentClasses->add('grow')) }}></div>
    </div>
@else
    <div
        role="separator"
        @if ($vertical) aria-orientation="vertical" @endif
        {{ $attributes->dataKey('separator')->whereDoesntStartWith(['content:'])->classes($contentClasses->add('shrink-0')) }}
    ></div>
@endif
