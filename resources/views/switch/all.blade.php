@props(['group' => null])
<tk:switch
    field:x-data="switchAll({ group: {{ Js::from($group) }} })"
    :$attributes
    label="Mark All"
/>
