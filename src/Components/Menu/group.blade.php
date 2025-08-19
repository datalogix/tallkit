<div {{
    $attributes
        ->whereDoesntStartWith(['separator-top:', 'heading:', 'separator-bottom:'])
        ->classes(
            '
            -mx-[.4rem] px-[.4rem]
            [&+&>[data-tallkit-menu-group-separator-top]]:hidden
            [&:first-child>[data-tallkit-menu-group-separator-top]]:hidden
            [&:last-child>[data-tallkit-menu-group-separator-bottom]]:hidden
            '
        )
        ->merge([
            'role' => 'group',
            'aria-labelledby' => $heading ? 'menu-group-'.Str::slug($heading) : false
        ])
}}>
    <tk:menu.separator :attributes="$attributesAfter('separator-top:')" />

    @if ($heading)
        <tk:menu.heading
            :attributes="$attributesAfter('heading:')"
            :label="$heading"
            :$size
            id="menu-group-{{ Str::slug($heading) }}"
        />
    @endif

    {{ $slot }}

    <tk:menu.separator :attributes="$attributesAfter('separator-bottom:')" />
</div>
