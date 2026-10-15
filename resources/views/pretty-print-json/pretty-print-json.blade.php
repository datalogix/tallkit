@props([
    'options' => null,
    'data' => null,
])
<tk:loadable
    x-data="prettyPrintJson"
    :attributes="$attributes->prefixed('loadable:')"
>
    <pre
        {{ $attributes->whereDoesntStartWith(['loadable:']) }}
        x-html="render(
            {{ Js::from($data ?? $slot) }},
            {{ Js::from($options) }}
        )"
    ></pre>
</tk:loadable>
