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
    $image = $iconName;
} else {
    $collections = [
        'material-symbols',
        'material-symbols-light',
        'ic',
        'mdi',
        'solar',
        'tabler',
        'hugeicons',
        'fluent',
        'ph',
        'heroicons',
        'arcticons',
        'openmoji',
        'game-icons',
    ];

    $names = array_unique(array_merge(
        Str::contains($iconName, ':') ? [$iconName] : [],
        Arr::map($collections, fn ($collection) => $collection.':'.Str::after($iconName, ':')),
    ));

    foreach ($names as $name) {
        if ($svg) {
            break;
        }

        $svg = TALLKit::getOrFetchSvgIcon($name);
    }
}

$isDecorative = ! $tooltip && ! $attributes->has('aria-label') && ! $attributes->has('aria-labelledby');

$ariaLabel = ! $attributes->has('aria-label') && ! $attributes->has('aria-labelledby') ? $tooltip : null;

@endphp
<tk:tooltip.wrapper :$attributes :$tooltip>
    @if ($image)
        <img
            src="{{ $image }}"
            {{
                $attributes
                    ->dataKey('icon')
                    ->classes('object-cover rounded', TALLKit::widthHeight($size))
                    ->when($isDecorative, fn ($attrs) => $attrs->merge(['aria-hidden' => 'true', 'alt' => '']))
                    ->when($ariaLabel, fn ($attrs, $value) => $attrs->merge(['aria-label' => __($value)]))
            }}
        />
    @else
        {!! Str::of($svg)->replace('<svg', '<svg '
            .($isDecorative ? 'aria-hidden="true" focusable="false" ' : '')
            .$attributes->dataKey('icon')->classes('text-current', TALLKit::widthHeight($size))
                ->when($ariaLabel, fn ($attrs, $value) => $attrs->merge(['aria-label' => __($value)]))) !!}
    @endif
</tk:tooltip.wrapper>
