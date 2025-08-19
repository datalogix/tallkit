@php
$icon ??= $attributes->pluck('icon') ?? $attributes->pluck('icon-checked') ?? 'check';
$iconUnchecked ??= $attributes->pluck('icon-unchecked');
@endphp

<tk:menu.item
    x-data="menuCheckbox"
    :attributes="$attributes->classes('group/menu-checkbox')->merge([
        'role' => 'menuitemcheckbox',
        'data-checked' => $checked,
    ])"
    :$variant
    :$size
    :$keepOpen
    icon:class="w-7"
>
    <x-slot name="icon">
        <tk:icon
            :attributes="$attributesAfter('icon-checked:')->classes('hidden group-data-checked/menu-checkbox:block')"
            :$icon
            size="sm"
        />
        <tk:icon
            :attributes="$attributesAfter('icon-unchecked:')->classes('block group-data-checked/menu-checkbox:hidden')"
            :icon="$iconUnchecked"
            size="sm"
        />
    </x-slot>

    {{ $slot }}
</tk:menu.item>
