@aware(['size'])
@props([
    'label' => null,
    'size' => null,
])
@php

$headingId = $label ? TALLKit::stableId('menu-group', $label) : null;

@endphp
<div
    {{
        $attributes
            ->dataKey('menu-group')
            ->whereDoesntStartWith(['separator-top:', 'heading:', 'separator-bottom:'])
            ->classes(
                '
                    -mx-[.4rem] px-[.4rem]
                    [&+&>[data-tallkit-menu-group-separator-top-container]]:hidden
                    [&:first-child>[data-tallkit-menu-group-separator-top-container]]:hidden
                    [&:last-child>[data-tallkit-menu-group-separator-bottom-container]]:hidden
                '
            )
            ->merge([
                'role' => 'group',
                'aria-labelledby' => $headingId ?: false
            ])
    }}
>
    <tk:menu.separator
        :attributes="$attributes->prefixed('separator-top:')
            ->merge(['container:'.TALLKit::dataKey(name: 'menu-group-separator-top-container') => ''])"
    />

    @if ($label)
        <tk:menu.heading
            :attributes="$attributes->prefixed('heading:')"
            :$label
            :$size
            :id="$headingId"
        />
    @endif

    {{ $slot }}

    <tk:menu.separator
        :attributes="$attributes->prefixed('separator-bottom:')
            ->merge(['container:'.TALLKit::dataKey(name: 'menu-group-separator-bottom-container') => ''])"
    />
</div>
