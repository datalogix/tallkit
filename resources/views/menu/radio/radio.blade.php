@props([
    'checked' => null,
    'keepOpen' => null,
])
@php

$icon ??= $attributes->pluck('icon') ?? $attributes->pluck('icon-checked') ?? 'check';
$iconUnchecked ??= $attributes->pluck('icon-unchecked');

@endphp
<tk:menu.item
    wire:ignore
    x-data="menuRadio({{ $checked }})"
    x-modelable="checked"
    :attributes="$attributes->classes('group/menu-radio')->merge(['data-keep-open' => $keepOpen])"
    role="menuitemradio"
    icon:class="w-7"
>
    <x-slot:icon>
        <tk:icon
            :attributes="TALLKit::attributesAfter($attributes, 'icon-checked:')->classes('hidden group-data-checked/menu-radio:block')"
            :$icon
            size="sm"
        />
        <tk:icon
            :attributes="TALLKit::attributesAfter($attributes, 'icon-unchecked:')->classes('block group-data-checked/menu-radio:hidden')"
            :icon="$iconUnchecked"
            size="sm"
        />
    </x-slot:icon>

    {{ $slot }}
</tk:menu.item>
