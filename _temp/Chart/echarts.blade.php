<tk:loadable
    x-data="echarts"
    :attributes="$attributesAfter('loadable:')"
>
    <div
        {{ $attributes->whereDoesntStartWith(['loadable:'])->classes('[:where(&)]:w-full [:where(&)]:h-100') }}
        x-init="render({{ $jsonOptions() }})"
    ></div>
</tk:loadable>
