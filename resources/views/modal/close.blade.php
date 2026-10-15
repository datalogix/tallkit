@props([
    ...TALLKit::elementProps(),
])
<tk:button :attributes="$attributes->withElementProps(get_defined_vars())->dataKey('modal-close')">
    {{ $slot }}
</tk:button>
