<tk:loadable
    x-data="frappeCharts"
    :attributes="$attributesAfter('loadable:')"
>
    <div
        {{ $attributes->whereDoesntStartWith(['loadable:']) }}
        x-init="render({{ $jsonOptions() }})"
    ></div>
</tk:loadable>
