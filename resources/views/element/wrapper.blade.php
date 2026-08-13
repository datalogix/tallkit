@props([
    ...TALLKit::elementProps(),
])
@if ($slot->hasActualContent() || $label)
    <tk:element
        :$attributes
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
    >
        {{ $slot }}
    </tk:element>
@endif
