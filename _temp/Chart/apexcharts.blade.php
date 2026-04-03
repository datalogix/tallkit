<tk:loadable
    x-data="apexcharts"
    :attributes="$attributesAfter('loadable:')"
>
    <div
        {{ $attributes->whereDoesntStartWith(['loadable:']) }}
        x-init="render({{ $jsonOptions() }})"
    ></div>
</tk:loadable>
