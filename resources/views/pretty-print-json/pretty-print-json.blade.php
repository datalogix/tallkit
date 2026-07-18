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
        x-html="render(@js($data ?? $slot), @js($options))"
    ></pre>
</tk:loadable>
