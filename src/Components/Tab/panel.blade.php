<div
    {{
        $attributes
            ->classes('[&:not([data-selected])]:hidden [:where(&)]:pt-4')
            ->merge(['data-selected' => $selected ? '' : false])
    }}
    wire:key="{{ $name }}"
    data-name="{{ $name }}"
    role="tabpanel"
    :tabindex="isSelected('{{ $name }}') ? 0 : -1"
    :data-selected="isSelected('{{ $name }}')"
>
    {{ $slot }}
</div>
