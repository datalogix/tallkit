<fieldset {{ $attributes->classes('
    [&[disabled]_[data-tallkit-label]]:opacity-50
    [&[disabled]_[data-tallkit-legend]]:opacity-50
    *:data-tallkit-field:mb-2.5
    [&>[data-tallkit-field]:has(>[data-tallkit-text])]:mb-4
    [&>[data-tallkit-field]:last-child]:mb-0!
    [&>[data-tallkit-legend]]:mb-3
    [&>[data-tallkit-legend]:has(+[data-tallkit-text])]:mb-2
    [&>[data-tallkit-legend]+[data-tallkit-text]]:mb-4
') }}>
    @if ($label || $legend || $description)
        <tk:legend
            :attributes="$attributesAfter('label:')
                ->merge($attributesAfter('legend:')->getAttributes())
                ->merge($attributesAfter('information:', prepend: true)->getAttributes())
                ->merge($attributesAfter('badge:', prepend: true)->getAttributes())
            "
            :label="$label ?? $legend"
            :$size
            :$information
            :$badge
            :class="$description ? 'mb-2' : 'mb-4'"
        />

        <tk:text
            :attributes="$attributesAfter('description:')"
            :label="$description"
            :$size
            class="mb-4"
        />
   @endif

    {{ $slot }}
</fieldset>
