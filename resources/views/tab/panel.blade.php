@props([
    'name' => null,
    'selected' => null,
])
@php

// The same count as its tab's: the Nth panel of a name pairs with the Nth tab.
$panelId = TALLKit::stableId('tab', $name, 'panel');
$tabId = Str::beforeLast($panelId, '-panel');

@endphp
<div
    {{
        $attributes
            ->classes('[&:not([data-selected])]:hidden')
            ->merge(['data-selected' => $selected ? '' : false])
            ->wireKey($panelId)
    }}
    data-name="{{ $name }}"
    id="{{ $panelId }}"
    aria-labelledby="{{ $tabId }}"
    role="tabpanel"
    :tabindex="isSelected(@js($name)) ? 0 : -1"
    :data-selected="isSelected(@js($name))"
>
    {{ $slot }}
</div>
