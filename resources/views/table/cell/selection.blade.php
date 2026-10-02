<tk:table.cell
    :attributes="$attributes->whereDoesntStartWith(['checkbox:'])->classes('p-2! w-8')"
    align="center"
>
    @if ($slot->hasActualContent())
        {{ $slot }}
    @else
        <tk:checkbox
            :attributes="$attributes->prefixed('checkbox:')->merge(['aria-label' => __('Select row')])"
            data-role="row-selection"
            size="sm"
        />
    @endif
</tk:table.cell>
