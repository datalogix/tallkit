@props([
    'id' => null,
    'size' => null,
    'label' => null,
    'legend' => null,
    'description' => null,
])
@php

$id ??= TALLKit::generateId('fieldset');
$descriptionId = "{$id}-description";

@endphp
<fieldset {{
    $attributes
        ->classes('
            [&[disabled]_[data-tallkit-label]]:opacity-50
            [&[disabled]_[data-tallkit-legend]]:opacity-50
            [&_[data-tallkit-field]]:mb-2
            [&>[data-tallkit-field]:has(>[data-tallkit-text])]:mb-4
            [&>[data-tallkit-field]:last-child]:mb-0
            [&>[data-tallkit-legend]]:mb-4
            [&>[data-tallkit-legend]:has(+[data-tallkit-text])]:mb-2
            [&>[data-tallkit-legend]+[data-tallkit-text]]:mb-4
        ')
        ->merge(['id' => $id])
        ->when($description, fn ($attrs) => $attrs->merge(['aria-describedby' => $descriptionId]))
}}>
    @if ($label || $legend || $description)
        <tk:legend
            :attributes="TALLKit::attributesAfter($attributes, 'label:', prepend: ['legend:', 'badge', 'info'])"
            :label="$label ?: $legend"
            :$size
        />

        <tk:text
            :attributes="TALLKit::attributesAfter($attributes, 'description:')
                ->classes('mb-4')
                ->when($description, fn ($attrs) => $attrs->merge(['id' => $descriptionId]))
            "
            :label="$description"
            :$size
        />
   @endif

    {{ $slot }}
</fieldset>
