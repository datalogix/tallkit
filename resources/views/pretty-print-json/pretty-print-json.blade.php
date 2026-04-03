@props([
    'options' => null,
    'data' => null,
])
<tk:loadable
    x-data="prettyPrintJson"
    :attributes="TALLKit::attributesAfter($attributes, 'loadable:')"
>
    <pre
        {{ $attributes->whereDoesntStartWith(['loadable:']) }}
        x-html="render(@js($slot ?? $data), @js($options))"
    ></pre>
</tk:loadable>
