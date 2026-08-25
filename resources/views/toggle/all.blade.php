@props(['group' => null])
<tk:toggle
    field:x-data="toggleAll({ group: {{ Js::from($group) }} })"
    :$attributes
    label="Mark All"
/>
