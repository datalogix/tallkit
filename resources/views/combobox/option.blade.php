@aware(['size'])
{{-- The label as a prop: left in the attributes, it would be escaped twice. --}}
@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'label' => null,
])
<tk:listbox.item
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())"
    :$size
    :$label
    content:class="block truncate"
    icon="check"
    icon:data-tallkit-checkmark
    icon:class="invisible shrink-0"
>
    {{ $slot }}
</tk:listbox.item>
