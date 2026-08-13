@props([
    'name' => null,
    'selected' => null,
])
<div
    {{
        $attributes
            ->classes('[&:not([data-selected])]:hidden')
            ->merge(['data-selected' => $selected ? '' : false])
    }}
    wire:key="{{ $name }}"
    data-name="{{ $name }}"
    id="{{ TALLKit::generateId('tabpanel', $name) }}"
    aria-labelledby="{{ TALLKit::generateId('tab', $name) }}"
    role="tabpanel"
    :tabindex="isSelected(@js($name)) ? 0 : -1"
    :data-selected="isSelected(@js($name))"
>
    {{ $slot }}
</div>
