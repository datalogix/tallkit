<tk:table.cell
    :attributes="$attributes->whereDoesntStartWith(['open:', 'close:'])->classes('p-1! w-8')"
    align="center"
>
    @if ($slot->isEmpty())
        <tk:button
            :attributes="TALLKit::attributesMerge($attributes->prefixed('open:'), $attributes->prefixed('close:'))->merge(['aria-label' => 'Row details'])"
            data-role="row-expanded"
            aria-expanded="false"
            icon="chevron-down"
            icon:class="transition-transform group-data-[expanded=open]:rotate-180"
            size="xs"
            variant="subtle"
        />
    @else
        {{ $slot }}
    @endif
</tk:table.cell>
