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
@if ($message)
    <div
        role="alert"
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
                :attributes="TALLKit::attributesAfter($attributes, 'icon:')"
                :name="is_string($icon) ? $icon : 'alert-outline'"
            />
        @endif

        {!! $message !!}
    </div>
@endif
