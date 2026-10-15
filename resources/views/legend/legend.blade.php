@props([
    ...TALLKit::elementProps(),
    'label' => null,
])
<tk:heading
    kind="legend"
    as="legend"
    :$label
    :attributes="$attributes->withElementProps(get_defined_vars())"
>
    {{ $slot }}
</tk:heading>
