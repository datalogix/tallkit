@php
$contentClasses = $classes(
    $backgroundColor(variant: $variant),
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

        <span {{ $attributesAfter('content:')->classes(
            'shrink mx-6 whitespace-nowrap',
            $fontSize(weight: true),
            $textColor(variant: $variant),
        ) }}>
            {{ $slot->isEmpty() ? __($label) : $slot }}
        </span>

        <div {{ $attributes->whereDoesntStartWith(['content:'])->classes($contentClasses->add('grow')) }}></div>
    </div>
@else
    <div role="none" {{ $attributes->whereDoesntStartWith(['content:'])->classes($contentClasses->add('shrink-0')) }}></div>
@endif
