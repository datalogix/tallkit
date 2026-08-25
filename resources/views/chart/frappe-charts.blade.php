@props([
    'options' => null,
])
<tk:loadable
    x-data="frappeCharts"
    :attributes="TALLKit::attributesAfter($attributes, 'loadable:')"
>
    <div
        {{ $attributes->whereDoesntStartWith(['loadable:']) }}
        x-ref="target"
        @if ($options) x-init="render(@js($options))" @endif
    ></div>
</tk:loadable>
