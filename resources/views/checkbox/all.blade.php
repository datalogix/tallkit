@props(['group' => null])
<tk:checkbox
    field:x-data="checkboxAll({ group: {{ Js::from($group) }} })"
    :$attributes
    indeterminate
    label="Mark All"
/>
