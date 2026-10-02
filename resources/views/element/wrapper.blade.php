@props([
    ...TALLKit::elementProps(),
    'info' => null,
])
@if ($slot->hasActualContent() || $label || $icon || $prefix || $suffix || $iconTrailing || $info || $badge || $prepend || $append || $kbd)
    <tk:element
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())"
        :$info
    >
        {{ $slot }}
    </tk:element>
@endif
