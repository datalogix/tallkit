@aware(['size'])
@props([
    'size' => null,
    'keepOpen' => null,
    'items' => null,
    'value' => null,
])
<tk:menu.group
    :attributes="$attributes->whereDoesntStartWith(['item:'])->merge(['data-keep-open' => $keepOpen])"
    :$size
    wire:ignore
    x-data="{ value: {{ Js::from($value ?? []) }} }"
    x-modelable="value"
>
    @foreach (collect($items) as $item)
        <tk:menu.checkbox
            :attributes="TALLKit::attributesAfter($attributes, 'item:')->merge(is_array($item) ? $item : ['label' => $item], false)"
            :$size
        />
    @endforeach

    {{ $slot }}
</tk:menu.group>
