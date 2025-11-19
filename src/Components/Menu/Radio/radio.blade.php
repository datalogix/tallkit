@php
$icon ??= $attributes->pluck('icon') ?? $attributes->pluck('icon-checked') ?? 'check';
$iconUnchecked ??= $attributes->pluck('icon-unchecked');
@endphp

<tk:menu.item
    x-data="menuRadio"
    :attributes="$attributes->classes('group/menu-radio')->merge([
        'role' => 'menuitemradio',
        'data-checked' => $checked,
    ])"
    icon:class="w-7"
>
    <x-slot:icon>
        <tk:icon
            :attributes="$attributesAfter('icon-checked:')->classes('hidden group-data-checked/menu-radio:block')"
            :$icon
            size="sm"
        />
        <tk:icon
            :attributes="$attributesAfter('icon-unchecked:')->classes('block group-data-checked/menu-radio:hidden')"
            :icon="$iconUnchecked"
            size="sm"
        />
    </x-slot:icon>

    {{ $slot }}
</tk:menu.item>
