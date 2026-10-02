@props([
    ...TALLKit::elementProps(),
])
<tk:text
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->merge(['label' => 'No records found', 'variant' => 'subtle'])->dataKey('listbox-no-records')"
    hidden
    class="p-4 text-center"
    aria-live="polite"
    role="status"
>
    {{ $slot }}
</tk:text>
