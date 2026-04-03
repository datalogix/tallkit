@props([
    'name' => null,
    'icon' => null,
    'size' => null,
    'image' => null,
    'svg' => null,
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

@endphp
<tk:tooltip.wrapper :$attributes>
    @if ($image)
        <img
            src="{{ $image }}"
            {{ $attributes->dataKey('icon')->classes('object-cover rounded', TALLKit::widthHeight($size)) }}
        />
    @else
        {!! Str::of($svg)->replace('<svg', '<svg '.$attributes->dataKey('icon')->classes('text-current', TALLKit::widthHeight($size))) !!}
    @endif
</tk:tooltip.wrapper>
