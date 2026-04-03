@props([
    'options' => null,
])
<tk:loadable
    x-data="echarts"
    :attributes="TALLKit::attributesAfter($attributes, 'loadable:')"
>
    <div
        {{ $attributes->whereDoesntStartWith(['loadable:'])->classes('[:where(&)]:w-full [:where(&)]:h-100') }}
        x-init="render(@js($options))"
    ></div>
</tk:loadable>
