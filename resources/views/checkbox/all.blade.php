@props([
    ...TALLKit::fieldProps(),
    'group' => null,
    'label' => 'Mark All',
])
<div
    x-data="checkboxAll({{ Js::from(['group' => $group]) }})"
    class="contents"
>
    <tk:checkbox
        :attributes="$attributes->withFieldProps(get_defined_vars(), ['iconOn' => null, 'iconOff' => null, 'iconIndeterminate' => null])"
        indeterminate
    />
</div>
