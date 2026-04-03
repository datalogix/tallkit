@if ($slot->hasActualContent() || $label)
    <tk:element :$attributes :$label>
        {{ $slot }}
    </tk:element>
@endif
