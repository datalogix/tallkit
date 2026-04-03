@props([
    'options' => null,
])
<tk:loadable
    x-data="chartjs"
    :attributes="TALLKit::attributesAfter($attributes, 'loadable:')"
>
    <canvas
        {{ $attributes->whereDoesntStartWith(['loadable:']) }}
        x-init="render(@js($options))"
    ></canvas>
</tk:loadable>
