@props([
    'tooltip' => null,
])
@if ($tooltip)
    <tk:tooltip
        :attributes="TALLKit::attributesAfter($attributes, 'tooltip:')"
        :content="$tooltip"
    >
        {{ $slot }}
    </tk:tooltip>
@else
    {{ $slot }}
@endif
