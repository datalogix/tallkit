@props([
    'tooltip' => null,
])
@if ($tooltip)
    <tk:tooltip
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'tooltip:')"
        :content="$tooltip"
    >
        {{ $slot }}
    </tk:tooltip>
@else
    {{ $slot }}
@endif
