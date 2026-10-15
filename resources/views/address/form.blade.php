@props([
    'card' => null,
    'size' => null,
    'lookup' => null,
    'required' => null,
    'name' => null,
])
@php

$lookup ??= config('tallkit.address.lookup', true);
$options = match (true) {
    is_array($lookup) => $lookup,
    is_string($lookup) => ['url' => $lookup],
    default => [],
};
$options += ['messages' => [
    'searching' => __('Searching zipcode...'),
    'filled' => __('Address filled.'),
    'failed' => __('Zipcode lookup failed. Fill in the address.'),
]];

$title = $attributes->get('title');
$titleId = is_string($title) && $title !== '' ? TALLKit::stableId('address-form', $name, 'title') : null;
$named = $attributes->has('aria-label') || $attributes->has('aria-labelledby');

$prefixes = ['zipcode:', 'address:', 'number:', 'complement:', 'neighborhood:', 'city:', 'state:', 'error:'];
$container = $attributes
    ->whereDoesntStartWith($prefixes)
    ->mergeDefined([
        'role' => 'group',
        'aria-labelledby' => $named ? null : $titleId,
        'aria-label' => $named || $titleId ? null : __('Address'),
        'title:id' => $named ? null : $titleId,
    ])
    ->merge($lookup ? [
        'x-data' => 'addressForm(' . Js::from($options) . ')',
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
            :$name
        />

        {{ $slot }}
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
            :$name
        />

        {{ $slot }}
    </tk:section>
@endif
