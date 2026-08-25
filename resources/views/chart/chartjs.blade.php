@props([
    'options' => null,
])
<tk:loadable
    x-data="chartjs"
    :attributes="TALLKit::attributesAfter($attributes, 'loadable:')"
>
    <canvas
        {{ $attributes->whereDoesntStartWith(['loadable:']) }}
        x-ref="target"
        @if ($options) x-init="render(@js($options))" @endif
    ></canvas>
</tk:loadable>
