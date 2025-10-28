<tfoot {{ $attributes
    ->whereDoesntStartWith(['row:', 'cell:'])
    ->classes('**:font-semibold **:text-zinc-900 **:dark:text-white')
}}>
    @if (Str::contains($slot, '<tr', true))
        {{ $slot }}
    @else
        <tk:table.row
            :attributes="$attributesAfter('row:')"
            role="row-foot"
        >
            @if (Str::contains($slot, '<td', true))
                {{ $slot }}
            @else
                <tk:table.cell :attributes="$attributesAfter('cell:')">
                    {{ $slot }}
                </tk:table.cell>
            @endif
        </tk:table.row>
    @endif
</tfoot>
