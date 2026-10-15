@props([
    ...TALLKit::elementProps(),
    'size' => null,
])
<tk:field.group.side
    side="suffix"
    :attributes="$attributes->withElementProps(get_defined_vars())"
    :$size
>
    {{ $slot }}
</tk:field.group.side>
