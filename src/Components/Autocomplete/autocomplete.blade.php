<div
    x-data="autocomplete({{ $jsonOptions() }})"
    {{ $attributesAfter('container:')->classes('[:where(&)]:w-full') }}
>
    <tk:input
        :attributes="$attributes->whereDoesntStartWith(['container:', 'popover:', 'items:'])"
        :$size
        autocomplete="off"
    />

    <tk:popover
        :attributes="$attributesAfter('popover:')->classes('p-0')"
        :$size
    >
        @if (Str::of($slot)->contains($dataKey('items')))
            {{ $slot }}
        @else
            <tk:autocomplete.items
                :attributes="$attributesAfter('items:')"
                :$items
                :$size
            >
                {{ $slot }}
            </tk:autocomplete.items>
        @endif
    </tk:popover>
</div>
