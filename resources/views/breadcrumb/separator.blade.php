@props([
    'icon' => null,
])
@php

$separatorClasses = TALLKit::classes('group-last/breadcrumb:hidden mx-2 opacity-75');

@endphp
@if ($icon == null)
    <tk:icon
        :attributes="$attributes->classes($separatorClasses, 'rtl:hidden')"
        icon="chevron-right"
    />
    <tk:icon
        :attributes="$attributes->classes($separatorClasses, 'hidden rtl:inline')"
        icon="chevron-left"
    />
@elseif (TALLKit::isSlot(slot: $icon))
    {{ $icon }}
@elseif ($icon === 'slash')
    <tk:icon
        :attributes="$attributes->classes($separatorClasses, 'rtl:-scale-x-100')"
        icon="slash"
    />
@else
    <tk:icon
        :attributes="$attributes->classes($separatorClasses)"
        :$icon
    />
@endif
