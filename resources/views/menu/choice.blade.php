@aware(['size'])
@props([
    ...TALLKit::elementProps(),
    'type' => 'checkbox',
    'checked' => null,
    'size' => null,
    'iconOn' => null,
    'iconOff' => null,
])
@php

$iconOn ??= 'check';

@endphp
{{-- Written whole: Tailwind only finds complete class names. --}}
<tk:menu.item
    wire:ignore
    x-data="{{ $type === 'radio' ? 'menuRadio' : 'menuCheckbox' }}({{ Js::from($checked) }})"
    x-modelable="checked"
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps(), ['keepOpen' => null])->whereDoesntStartWith(['icon-on:', 'icon-off:'])->classes('group/menu-choice')"
    :role="$type === 'radio' ? 'menuitemradio' : 'menuitemcheckbox'"
    :$size
    :icon:class="TALLKit::classes('w-7', TALLKit::widthHeight(size: TALLKit::adjustSize(size: $size)))"
>
    <x-slot:icon>
        <tk:icon
            :attributes="$attributes->prefixed('icon-on:')->classes('hidden group-data-checked/menu-choice:block')"
            :icon="$iconOn"
            :size="TALLKit::adjustSize(size: $size)"
        />
        <tk:icon
            :attributes="$attributes->prefixed('icon-off:')->classes('block group-data-checked/menu-choice:hidden')"
            :icon="$iconOff"
            :size="TALLKit::adjustSize(size: $size)"
        />
    </x-slot:icon>

    {{ $slot }}
</tk:menu.item>
