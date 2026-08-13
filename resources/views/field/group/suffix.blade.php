@props([
    'size' => null,
    ...TALLKit::elementProps(),
])
<tk:field.group.side
    side="suffix"
    :$size
    :$label
    :$icon
    :$prefix
    :$suffix
    :$iconTrailing
    :$info
    :$badge
    :$prepend
    :$append
    :$kbd
    :$attributes
>
    {{ $slot }}
</tk:field.group.side>
