@props([
    ...TALLKit::fieldProps(),
    'group' => null,
    'label' => 'Mark All',
])
<div x-data="checkboxAll({ group: @js($group) })" class="contents">
    <tk:checkbox
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldProps(), ['iconOn' => null, 'iconOff' => null, 'iconIndeterminate' => null])"
        indeterminate
    />
</div>
