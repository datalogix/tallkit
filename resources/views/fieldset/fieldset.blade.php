@props([
    'size' => null,
    'label' => null,
    'legend' => null,
    'description' => null,
])
<fieldset {{ $attributes->classes('
    [&[disabled]_[data-tallkit-label]]:opacity-50
    [&[disabled]_[data-tallkit-legend]]:opacity-50
    [&_[data-tallkit-field]]:mb-3
    [&>[data-tallkit-field]:has(>[data-tallkit-text])]:mb-4
    [&>[data-tallkit-field]:last-child]:mb-0
    [&>[data-tallkit-legend]]:mb-4
    [&>[data-tallkit-legend]:has(+[data-tallkit-text])]:mb-2
    [&>[data-tallkit-legend]+[data-tallkit-text]]:mb-4
') }}>
    @if ($label || $legend || $description)
        <tk:legend
            :attributes="TALLKit::attributesAfter($attributes, 'label:', prepend: ['legend:', 'badge', 'info'])"
            :label="$label ?: $legend"
            :$size
        />

        <tk:text
            :attributes="TALLKit::attributesAfter($attributes, 'description:')->classes('mb-4')"
            :label="$description"
            :$size
        />
   @endif

    {{ $slot }}
</fieldset>
