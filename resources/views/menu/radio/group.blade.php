@props([
    'keepOpen' => null,
    'items' => null,
])
<div
    wire:ignore
    x-data="{ value: null }"
    x-modelable="value"
    {{ $attributes->whereDoesntStartWith(['item:'])->merge(['data-keep-open' => $keepOpen]) }}
>
    @foreach (collect($items) as $item)
        <tk:menu.radio
            :attributes="TALLKit::attributesAfter($attributes, 'item:')->merge(is_array($item) ? $item : ['label' => $item], false)"
        />
    @endforeach

    {{ $slot }}
</div>
