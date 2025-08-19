<div
    x-data="submenu"
    {{ $attributesAfter('container:') }}
>
    <tk:menu.item
        :attributes="$attributes->whereDoesntStartWith(['container:', 'suffix:', 'icon:', 'icon-trailing:', 'menu:'])"
        :$icon
        keep-open
    >
        {{ $heading }}

        <x-slot name="suffix" {{ $attributesAfter('suffix:')->classes('ms-auto') }}>
            @if (is_string($iconTrailing) && $iconTrailing !== '')
                <tk:icon
                    :icon="$iconTrailing"
                    :attributes="$attributesAfter('icon-trailing:')->classes('text-zinc-400 [[data-tallkit-menu-item]:hover_&]:text-current')"
                />
            @elseif ($iconTrailing)
                {{ $iconTrailing }}
            @else
                <tk:icon
                    icon="chevron-right"
                    :attributes="$attributesAfter('icon:')->classes('text-zinc-400 [[data-tallkit-menu-item]:hover_&]:text-current rtl:hidden')"
                />
                <tk:icon
                    icon="chevron-left"
                    :attributes="$attributesAfter('icon:')->classes('text-zinc-400 [[data-tallkit-menu-item]:hover_&]:text-current hidden rtl:inline')"
                />
            @endif
        </x-slot>
    </tk:menu.item>

    <tk:menu :attributes="$attributesAfter('menu:')->classes('-ml-2')">
        {{ $slot }}
    </tk:menu>
</div>
