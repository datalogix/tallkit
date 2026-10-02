@aware(['toggleable' => null])
@props([
    'name' => null,
    'fixed' => null,
    'hidden' => null,
])
@php

$columnAttributes = $fixed !== null && $fixed !== ''
    ? TALLKit::attributesMerge([
        'data-column-fixed' => $fixed,
        'x-bind:style' => 'columnFixedStyle('.Js::from($fixed).')',
    ])
    : TALLKit::attributesMerge(
        ['x-bind:style' => $name !== null && $name !== '' ? 'columnStyle('.Js::from($name).')' : null],
        TALLKit::tableColumnAttributes(name: $name, toggleable: (bool) $toggleable, hidden: (bool) $hidden),
    );

@endphp
<col {{ $attributes->mergeDefined($columnAttributes, escape: false) }}>
