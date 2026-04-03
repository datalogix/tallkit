@props([
    'heading' => null,
    'size' => null,
])
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
                'aria-labelledby' => $heading ? 'menu-group-'.Str::slug($heading) : false
            ])
    }}
>
    <tk:menu.separator
        :attributes="TALLKit::attributesAfter($attributes, 'separator-top:')"
        {{ 'container:'.TALLKit::dataKey('menu-group-separator-top-container') }}
    />

    @if ($heading)
        <tk:menu.heading
            :attributes="TALLKit::attributesAfter($attributes, 'heading:')"
            :label="$heading"
            :$size
            id="menu-group-{{ Str::slug($heading) }}"
        />
    @endif

    {{ $slot }}

    <tk:menu.separator
        :attributes="TALLKit::attributesAfter($attributes, 'separator-bottom:')"
        {{ 'container:'.TALLKit::dataKey('menu-group-separator-bottom-container') }}
    />
</div>
