@props([
    ...TALLKit::elementProps(),
    'data' => null,
    'zipcode' => null,
    'address' => null,
    'number' => null,
    'complement' => null,
    'neighborhood' => null,
    'city' => null,
    'state' => null,
    'inline' => null,
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
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->merge(['icon' => 'map-marker', 'scale' => 'large'])"
>
    @php
        $street = implode(', ', array_filter([$address, $number, $complement, $neighborhood]));
        $place = implode(', ', array_filter([implode(' - ', array_filter([$city, $state])), $zipcode]));
    @endphp
    {{ $street }}

    @if ($place !== '')
        @if ($street !== '')
            {!! $inline ? ', ' : '<br>' !!}
        @endif
        {{ $place }}
    @endif

    {{ $slot }}
</tk:text>
