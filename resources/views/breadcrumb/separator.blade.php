@props([
    'icon' => null,
])
@php

$separatorClasses = TALLKit::classes('group-last/breadcrumb:hidden mx-2 opacity-75');

@endphp
@if ($icon == null)
    <tk:icon
        :attributes="$attributes->classes($separatorClasses, 'rtl:-scale-x-100')"
        icon="chevron-right"
    />
@elseif (TALLKit::isSlot(slot: $icon))
    <span {{ $attributes->classes($separatorClasses, 'inline-flex items-center')->merge(['aria-hidden' => 'true']) }}>
        {{ $icon }}
    </span>
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
