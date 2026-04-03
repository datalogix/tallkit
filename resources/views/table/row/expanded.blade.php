<tk:table.row
    :attributes="TALLKit::attributesAfter($attributes, 'row:')->classes('hidden [[data-expanded=open]+&]:table-row')"
    role="row-expanded"
>
    <tk:table.cell :attributes="$attributes->whereDoesntStartWith(['row:'])">
        {{ $slot }}
    </tk:table.cell>
</tk:table.row>
