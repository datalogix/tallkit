@props([
    // element
    'label' => null,
    'icon' => null,
    'prefix' => null,
    'suffix' => null,
    'iconTrailing' => null,
    'info' => null,
    'badge' => null,
    'prepend' => null,
    'append' => null,
    'kbd' => null,
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
