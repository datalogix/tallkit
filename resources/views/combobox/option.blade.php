@aware(['size'])
@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'label' => null,
])
<tk:listbox.item
    :attributes="$attributes->withElementProps(get_defined_vars())"
    :$size
    :$label
    content:class="block truncate"
    icon="check"
    icon:data-tallkit-checkmark
    icon:class="invisible shrink-0"
>
    {{ $slot }}
</tk:listbox.item>
