@props([
    'name' => null,
    'selected' => null,
])
<div
    {{
        $attributes
            ->classes('[&:not([data-selected])]:hidden')
            ->merge(['data-selected' => $selected ? '' : false])
            ->merge(in_livewire() ? ['wire:key' => $name] : [], false)
    }}
    data-name="{{ $name }}"
    id="{{ TALLKit::generateId(prefix: 'tabpanel', name: $name) }}"
    aria-labelledby="{{ TALLKit::generateId(prefix: 'tab', name: $name) }}"
    role="tabpanel"
    :tabindex="isSelected(@js($name)) ? 0 : -1"
    :data-selected="isSelected(@js($name))"
>
    {{ $slot }}
</div>
