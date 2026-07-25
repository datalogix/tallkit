@aware(['size'])
@props(['size' => null])
<tk:listbox.item
    :$attributes
    :$size
    content:class="block truncate"
    icon="check"
    icon:data-tallkit-checkmark
    icon:class="invisible shrink-0"
>
    {{ $slot }}
</tk:listbox.item>
