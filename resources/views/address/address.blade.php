@props([
    'data' => null,
    'zipcode' => null,
    'address' => null,
    'number' => null,
    'complement' => null,
    'neighborhood' => null,
    'city' => null,
    'state' => null,
    'inline' => false,
])
@php

$zipcode ??= data_get($data, 'zipcode');
$address ??= data_get($data, 'address');
$number ??= data_get($data, 'number');
$complement ??= data_get($data, 'complement');
$neighborhood ??= data_get($data, 'neighborhood');
$city ??= data_get($data, 'city');
$state ??= data_get($data, 'state');

@endphp
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
