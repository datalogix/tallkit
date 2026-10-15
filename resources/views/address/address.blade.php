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

$digits = preg_replace('/\D/', '', (string) $zipcode);
$zipcode = strlen($digits) === 8 ? substr($digits, 0, 5).'-'.substr($digits, 5) : $zipcode;
$street = implode(', ', array_filter([$address, $number, $complement, $neighborhood], 'filled'));
$place = implode(', ', array_filter([implode(' - ', array_filter([$city, $state], 'filled')), $zipcode], 'filled'));

@endphp
@if ($street !== '' || $place !== '' || $slot->hasActualContent())
    <tk:text
        as="address"
        :attributes="$attributes->withElementProps(get_defined_vars())->merge(['icon' => 'map-marker', 'scale' => 'large'])"
    >
        {{ $street }}

        @if ($place !== '')
            @if ($street !== '')
                {!! $inline ? ', ' : '<br>' !!}
            @endif
            {{ $place }}
        @endif

        {{ $slot }}
    </tk:text>
@endif
