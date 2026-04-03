<tk:loadable
    x-data="prettyPrintJson"
    :attributes="$attributesAfter('loadable:')"
>
    <pre
        {{ $attributes->whereDoesntStartWith(['loadable:']) }}
        x-html="render(@js($slot ?? $data), {{ $jsonOptions() }})"
    ></pre>
</tk:loadable>
