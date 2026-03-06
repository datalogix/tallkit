<tk:text
    as="address"
    :$attributes
    icon="map-marker"
    mode="large"
>
    {{ implode(', ', array_filter([$address, $number, $complement, $neighborhood])) }}

    @if ($city || $state || $zipcode)
        {!! $inline ? ',' : '<br>' !!}
        {{ $city }}
        {{ $state ? ' - ' : '' }}
        {{ $state }}
        {{ $zipcode ? ', ' : '' }}
        {{ $zipcode }}
    @endif

    {{ $slot }}
</tk:text>
