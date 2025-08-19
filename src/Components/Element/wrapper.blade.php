@if ($slot->isNotEmpty() || $label)
    <tk:element :$attributes :$label>
        {{ $slot }}
    </tk:element>
@endif
