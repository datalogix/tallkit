<div
    x-data="autocomplete"
    {{ $attributesAfter('container:') }}
>
    <tk:input
        :attributes="$attributes->whereDoesntStartWith(['container:', 'popover:', 'items:'])"
        autocomplete="off"
    />

    <tk:popover :attributes="$attributesAfter('popover:')->classes('p-0')">
        @if (Str::of($slot)->contains($dataKey('items')))
            {{ $slot }}
        @else
            <tk:autocomplete.items
                :attributes="$attributesAfter('items:')"
                :$items
            >
                {{ $slot }}
            </tk:autocomplete.items>
        @endif
    </tk:popover>
</div>
