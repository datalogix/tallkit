<tk:table.row :attributes="$attributesAfter('row:')" role="no-records">
    <tk:table.cell :attributes="$attributes->whereDoesntStartWith(['row:'])">
        {{ $slot->isEmpty() ? __('No records found') : $slot }}
    </tk:table.cell>
</tk:table.row>
