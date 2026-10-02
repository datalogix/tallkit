<tk:table.column
    :attributes="$attributes->whereDoesntStartWith(['checkbox:'])->classes('p-2! w-8')"
    :sortable="false"
    align="center"
>
    @if ($slot->hasActualContent())
        {{ $slot }}
    @else
        <tk:checkbox
            :attributes="$attributes->prefixed('checkbox:')->merge(['aria-label' => __('Select all rows')])"
            x-model="selectAllChecked"
            x-on:change="toggleAll"
            :label="false"
            size="sm"
        />
    @endif
</tk:table.column>
