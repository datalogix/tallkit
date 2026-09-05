@props([
    'bag' => null,
    'size' => null,
    'name' => null,
    'message' => null,
    'icon' => null,
])
@php

$message ??= TALLKit::getError(name: $name, slot: $slot, bag: $bag);

@endphp
@if (filled($message))
    <div
        role="status"
        aria-live="polite"
        aria-atomic="true"
        {{
            $attributes
                ->dataKey('error')
                ->whereDoesntStartWith(['icon:'])
                ->classes(
                    'flex items-center text-red-500 dark:text-red-400',
                    TALLKit::fontSize(size: $size),
                    TALLKit::iconSize(size: $size),
                    TALLKit::gap(size: $size),
                )
        }}
    >
        @if ($message && $icon !== false)
            <tk:icon
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon:')"
                :icon="is_string($icon) ? $icon : 'alert-outline'"
            />
        @endif

        {!! $message !!}
    </div>
@endif
