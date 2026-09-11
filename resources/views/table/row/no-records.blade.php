<tk:table.row :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'row:')" data-role="no-records">
    <tk:table.cell :attributes="$attributes->whereDoesntStartWith(['row:'])">
        <tk:text variant="subtle">
            {{ $slot->isEmpty() ? __('No records found') : $slot }}
        </tk:text>
    </tk:table.cell>
</tk:table.row>
