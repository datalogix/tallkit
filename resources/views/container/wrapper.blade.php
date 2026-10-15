@props([
    'container' => null,
])
@if ($container)
    <tk:container
        :$attributes
        :size="string_value($container)"
    >
        {{ $slot }}
    </tk:container>
@else
    {{ $slot }}
@endif
