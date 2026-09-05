@props([
    'name' => null,
    'icon' => null,
    'size' => null,
    'image' => null,
    'svg' => null,
    'tooltip' => null,
])
@php

$iconName = $name ?? $icon;

if (Str::isUrl($iconName)) {
    $image ??= $iconName;
} else {
    $svg ??= TALLKit::getOrFetchSvgIcon(name: $iconName);
}

$isDecorative = ! $tooltip && ! $attributes->has('aria-label') && ! $attributes->has('aria-labelledby');
$ariaLabel = ! $attributes->has('aria-label') && ! $attributes->has('aria-labelledby') ? $tooltip : null;

@endphp
@if ($image || $svg || $slot->isNotEmpty())
    <tk:tooltip.wrapper :$attributes :$tooltip>
        @if ($image)
            <img
                src="{{ $image }}"
                {{
                    $attributes
                        ->dataKey('icon')
                        ->classes('object-cover rounded', TALLKit::widthHeight(size: $size))
                        ->when($isDecorative, fn ($attrs) => $attrs->merge(['aria-hidden' => 'true', 'alt' => '']))
                        ->when($ariaLabel, fn ($attrs, $value) => $attrs->merge(['aria-label' => __($value)]))
                }}
            />
        @elseif($svg)
            {!! Str::of($svg)->replaceFirst('<svg', '<svg '
                .($isDecorative ? 'aria-hidden="true" focusable="false" ' : 'role="img" ')
                .$attributes->dataKey('icon')->classes('text-current', TALLKit::widthHeight(size: $size))
                    ->when($ariaLabel, fn ($attrs, $value) => $attrs->merge(['aria-label' => __($value)]))) !!}
        @else
            {{ $slot }}
        @endif
    </tk:tooltip.wrapper>
@endif
