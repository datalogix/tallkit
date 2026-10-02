@props([
    'card' => null,
    'size' => null,
    'autocomplete' => null,
    'required' => null,
])
@php

$prefixes = ['zipcode:', 'address:', 'number:', 'complement:', 'neighborhood:', 'city:', 'state:'];
$container = $attributes
    ->whereDoesntStartWith($prefixes)
    ->merge($autocomplete !== false ? [
        'x-data' => 'addressForm(' . Js::from(is_array($autocomplete) ? $autocomplete : []) . ')',
    ] : []);

@endphp
@if ($card)
    <tk:card
        :attributes="$container"
        :$size
    >
        <tk:address.fields
            :attributes="$attributes->whereStartsWith($prefixes)"
            :$size
            :$required
        />
    </tk:card>
@else
    <tk:section
        :attributes="$container"
        :$size
    >
        <tk:address.fields
            :attributes="$attributes->whereStartsWith($prefixes)"
            :$size
            :$required
        />
    </tk:section>
@endif
