<tk:table.cell
    :attributes="$attributes->whereDoesntStartWith(['checkbox:'])->classes('p-2! w-8')"
    align="center"
>
    @if ($slot->hasActualContent())
        {{ $slot }}
    @else
        <tk:checkbox
            :attributes="$attributesAfter('checkbox:')"
            role="row-selection"
            size="sm"
        />
    @endif
</tk:table.cell>
