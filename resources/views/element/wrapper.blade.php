@props([
    ...TALLKit::elementProps(),
    'info' => null,
])
@if ($slot->hasActualContent() || has_content($label, $icon, $prefix, $suffix, $iconTrailing, $info, $badge, $prepend, $append, $kbd))
    <tk:element
        :attributes="$attributes->withElementProps(get_defined_vars())"
        :$info
    >
        {{ $slot }}
    </tk:element>
@endif
