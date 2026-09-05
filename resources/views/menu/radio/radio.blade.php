@aware(['size'])
@props([
    'checked' => null,
    'size' => null,
    'icon' => null,
    'iconUnchecked' => null,
])
@php

$icon ??= $attributes->pluck('icon') ?? $attributes->pluck('icon-checked') ?? 'check';
$iconUnchecked ??= $attributes->pluck('icon-unchecked');

@endphp
<tk:menu.item
    wire:ignore
    x-data="menuRadio({{ Js::from($checked) }})"
    x-modelable="checked"
    :attributes="$attributes->whereDoesntStartWith(['icon-checked:', 'icon-unchecked:'])->classes('group/menu-radio')"
    role="menuitemradio"
    :$size
    icon:class="w-7"
>
    <x-slot:icon>
        <tk:icon
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-checked:')->classes('hidden group-data-checked/menu-radio:block')"
            :$icon
            :size="TALLKit::adjustSize(size: $size)"
        />
        <tk:icon
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-unchecked:')->classes('block group-data-checked/menu-radio:hidden')"
            :icon="$iconUnchecked"
            :size="TALLKit::adjustSize(size: $size)"
        />
    </x-slot:icon>

    {{ $slot }}
</tk:menu.item>
