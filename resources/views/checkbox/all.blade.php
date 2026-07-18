@props(['group'])
<tk:checkbox
    field:x-data="checkboxAll({ group: '{{ $group }}' })"
    :$attributes
    indeterminate
    label="Mark All"
/>
