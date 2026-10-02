@php($slotHas = fn (string $tag) => Str::contains($slot, '<'.$tag, true))
<tfoot {{ $attributes
    ->whereDoesntStartWith(['row:', 'cell:'])
    ->classes('*:font-semibold', TALLKit::textNeutral(variant: 'strong', prefix: '*:'))
}}>
    @if ($slotHas('tr'))
        {{ $slot }}
    @else
        <tk:table.row
            :attributes="$attributes->prefixed('row:')"
            data-role="row-foot"
        >
            @if ($slotHas('td'))
                {{ $slot }}
            @else
                <tk:table.cell :attributes="$attributes->prefixed('cell:')">
                    {{ $slot }}
                </tk:table.cell>
            @endif
        </tk:table.row>
    @endif
</tfoot>
