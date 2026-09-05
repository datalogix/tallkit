@props([
    'options' => null,
])
<tk:loadable
    x-data="echarts"
    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'loadable:')"
>
    <div
        {{ $attributes->whereDoesntStartWith(['loadable:'])->classes('[:where(&)]:w-full [:where(&)]:h-100') }}
        x-ref="target"
        @if ($options) x-init="render(@js($options))" @endif
    ></div>
</tk:loadable>
