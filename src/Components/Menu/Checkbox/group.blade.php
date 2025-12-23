<div
    wire:ignore
    x-data="{ value: null }"
    x-modelable="value"
    {{ $attributes->whereDoesntStartWith(['item:'])->merge(['data-keep-open' => $keepOpen]) }}
>
    @foreach (collect($items) as $item)
        <tk:menu.checkbox
            :attributes="$attributesAfter('item:')->merge($item, false)"
        />
    @endforeach

    {{ $slot }}
</div>
