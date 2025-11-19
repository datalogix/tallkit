<tk:table.cell
    :attributes="$attributes->whereDoesntStartWith(['open:', 'close:'])->classes('p-1! w-8')"
    align="center"
>
    @if ($slot->isEmpty())
        <tk:button
            :attributes="$attributesAfter('open:')->classes('hidden! group-data-[expanded=close]:flex!')"
            role="row-expanded"
            icon="chevron-down"
            size="xs"
            variant="subtle"
        />
        <tk:button
            :attributes="$attributesAfter('close:')->classes('hidden! group-data-[expanded=open]:flex!')"
            role="row-expanded"
            icon="chevron-up"
            size="xs"
            variant="subtle"
        />
    @else
        {{ $slot }}
    @endif
</tk:table.cell>
