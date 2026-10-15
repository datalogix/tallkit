@props([
    ...TALLKit::fieldProps(),
    'group' => null,
    'label' => 'Mark All',
])
<div
    x-data="switchAll({{ Js::from(['group' => $group]) }})"
    class="contents"
>
    <tk:switch
        :attributes="$attributes->withFieldProps(get_defined_vars(), [
            'iconOn' => null,
            'iconOff' => null,
            'labelOn' => null,
            'labelOff' => null,
            'loadingDelay' => null,
            'loadingMinDuration' => null
        ])"
    />
</div>
