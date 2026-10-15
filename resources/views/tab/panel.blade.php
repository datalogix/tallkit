@props([
    'name' => null,
    'selected' => null,
])
@php

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
    :tabindex="isSelected({{ Js::from($name) }}) ? 0 : -1"
    :data-selected="isSelected({{ Js::from($name) }})"
>
    {{ $slot }}
</div>
