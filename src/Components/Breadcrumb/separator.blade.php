@php
$separatorClasses = $classes('group-last/breadcrumb:hidden mx-2 opacity-75');
@endphp

@if ($icon == null)
    <tk:icon
        :attributes="$attributes->classes($separatorClasses, 'rtl:inline')"
        :$size
        icon="chevron-right"
    />
    <tk:icon
        :attributes="$attributes->classes($separatorClasses, 'hidden rtl:inline')"
        :$size
        icon="chevron-left"
    />
@elseif ($isSlot($icon))
    {{ $icon }}
@elseif ($icon === 'slash')
    <tk:icon
        :attributes="$attributes->classes($separatorClasses, 'rtl:-scale-x-100')"
        :$size
        icon="slash"
    />
@else
    <tk:icon
        :attributes="$attributes->classes($separatorClasses)"
        :$icon
        :$size
    />
@endif
