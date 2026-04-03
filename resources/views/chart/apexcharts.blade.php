@props([
    'options' => null,
])
<tk:loadable
    x-data="apexcharts"
    :attributes="TALLKit::attributesAfter($attributes, 'loadable:')"
>
    <div
        {{ $attributes->whereDoesntStartWith(['loadable:']) }}
        x-init="render(@js($options))"
    ></div>
</tk:loadable>
