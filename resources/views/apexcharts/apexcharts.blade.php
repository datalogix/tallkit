@props([
    'options' => null,
])
<tk:loadable
    x-data="apexcharts"
    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'loadable:')"
>
    <div
        {{ $attributes->whereDoesntStartWith(['loadable:']) }}
        x-ref="target"
        @if ($options) x-init="render(@js($options))" @endif
    ></div>
</tk:loadable>
