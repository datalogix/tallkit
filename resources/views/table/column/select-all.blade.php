<tk:table.column
    :attributes="$attributes->whereDoesntStartWith(['checkbox:'])->classes('p-2! w-8')"
    :sortable="false"
    align="center"
>
    @if ($slot->hasActualContent())
        {{ $slot }}
    @else
        <tk:checkbox
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'checkbox:')"
            x-model="selectAllChecked"
            x-on:change="toggleAll"
            :label="false"
            aria-label="{{ __('Select all rows') }}"
            size="sm"
        />
    @endif
</tk:table.column>
