@if ($tooltip)
    <tk:tooltip
        :attributes="$attributesAfter('tooltip:')"
        :content="$tooltip"
    >
        {{ $slot }}
    </tk:tooltip>
@else
    {{ $slot }}
@endif
