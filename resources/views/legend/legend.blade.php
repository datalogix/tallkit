@props([
    'label' => null,
])
<tk:heading
    name="legend"
    as="legend"
    :$label
    :$attributes
>
    {{ $slot }}
</tk:heading>
