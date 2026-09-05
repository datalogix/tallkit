<tfoot {{ $attributes
    ->whereDoesntStartWith(['row:', 'cell:'])
    ->classes('*:font-semibold *:text-zinc-800 dark:*:text-white')
}}>
    @if (Str::contains($slot, '<tr', true))
        {{ $slot }}
    @else
        <tk:table.row
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'row:')"
            data-role="row-foot"
        >
            @if (Str::contains($slot, '<td', true))
                {{ $slot }}
            @else
                <tk:table.cell :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'cell:')">
                    {{ $slot }}
                </tk:table.cell>
            @endif
        </tk:table.row>
    @endif
</tfoot>
