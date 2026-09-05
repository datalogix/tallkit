<tk:table.row
    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'row:')->classes('hidden [[data-expanded=open]+&]:table-row')"
    data-role="row-expanded"
>
    <tk:table.cell :attributes="$attributes->whereDoesntStartWith(['row:'])">
        {{ $slot }}
    </tk:table.cell>
</tk:table.row>
