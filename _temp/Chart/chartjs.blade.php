<tk:loadable
    x-data="chartjs"
    :attributes="$attributesAfter('loadable:')"
>
    <canvas
        {{ $attributes->whereDoesntStartWith(['loadable:']) }}
        x-init="render({{ $jsonOptions() }})"
    ></canvas>
</tk:loadable>
