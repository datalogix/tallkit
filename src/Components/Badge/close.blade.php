<tk:element :attributes="$attributes->whereDoesntStartWith(['icon:'])->classes('p-1 -my-1 -me-1 opacity-50 hover:opacity-100')" as="button" data-tallkit-badge-close>
    @if ($slot->isEmpty())
        <tk:icon :attributes="$attributesAfter('icon:')" :icon="$icon ?? 'close'" size="sm" data-tallkit-badge-close-icon />
    @else
        {{ $slot }}
    @endif
</tk:element>
