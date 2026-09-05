<tk:table.cell
    :attributes="$attributes->whereDoesntStartWith(['open:', 'close:'])->classes('p-1! w-8')"
    align="center"
>
    @if ($slot->isEmpty())
        <tk:button
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'open:')->classes('hidden! group-data-[expanded=close]:flex!')"
            data-role="row-expanded"
            icon="chevron-down"
            size="xs"
            variant="subtle"
        />
        <tk:button
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'close:')->classes('hidden! group-data-[expanded=open]:flex!')"
            data-role="row-expanded"
            icon="chevron-up"
            size="xs"
            variant="subtle"
        />
    @else
        {{ $slot }}
    @endif
</tk:table.cell>
