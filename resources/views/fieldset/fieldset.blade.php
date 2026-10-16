@props([
    'id' => null,
    'size' => null,
    'label' => null,
    'description' => null,
])
@php

$id ??= TALLKit::stableId('fieldset');
$descriptionId = "{$id}-description";

@endphp
<fieldset {{
    $attributes
        ->whereDoesntStartWith(['label:', 'badge', 'info', 'description:'])
        ->classes(
            '
                min-w-0
                [&[disabled]_[data-tallkit-label]]:opacity-disabled
                [&[disabled]_[data-tallkit-legend]]:opacity-disabled
                [&_[data-tallkit-field]]:mb-2
                [&>[data-tallkit-field]:has(>[data-tallkit-text])]:mb-4
                [&>[data-tallkit-field]:last-child]:mb-0
                [&>[data-tallkit-legend]]:mb-4
                [&>[data-tallkit-legend]:has(+[data-tallkit-text])]:mb-2
                [&>[data-tallkit-legend]+[data-tallkit-text]]:mb-4
            '
        )
        ->except('aria-describedby')
        ->merge([
            'id' => $id,
            'aria-describedby' => collect([$attributes->get('aria-describedby'), has_content($description) ? $descriptionId : null])->filter()->implode(' ') ?: null,
        ])
}}>
    @if (has_content($label, $description))
        <tk:legend
            :attributes="$attributes->prefixed('label:', with: ['badge', 'info'])"
            :$label
            :$size
        />

        <tk:text
            :attributes="$attributes->prefixed('description:')
                ->classes('mb-4')
                ->when(has_content($description), fn ($attrs) => $attrs->merge(['id' => $descriptionId]))
            "
            :label="$description"
            :$size
        />
   @endif

    {{ $slot }}
</fieldset>
