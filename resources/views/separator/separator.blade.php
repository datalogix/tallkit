@props([
    'vertical' => null,
    'label' => null,
])
@php

$contentClasses = TALLKit::classes(
    'bg-zinc-800/20 dark:bg-white/20',
    'border-0 [print-color-adjust:exact]',
    match ($vertical) {
        true => 'self-stretch self-center w-px h-full',
        default => 'h-px w-full',
    },
);

@endphp
@if ($slot->hasActualContent() || $label)
    <div role="none" class="flex items-center w-full">
        <div {{ $attributes->whereDoesntStartWith(['content:'])->classes($contentClasses->add('grow')) }}></div>

        <span {{ TALLKit::attributesAfter($attributes, 'content:')->classes(
            'shrink mx-6 whitespace-nowrap',
            'text-zinc-500 dark:text-zinc-300',
            TALLKit::fontSize(weight: true),
        ) }}>
            {{ $slot->isEmpty() ? __($label) : $slot }}
        </span>

        <div {{ $attributes->whereDoesntStartWith(['content:'])->classes($contentClasses->add('grow')) }}></div>
    </div>
@else
    <div role="none" {{ $attributes->whereDoesntStartWith(['content:'])->classes($contentClasses->add('shrink-0')) }}></div>
@endif
