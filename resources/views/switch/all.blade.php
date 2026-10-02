@props([
    ...TALLKit::fieldProps(),
    'group' => null,
    'label' => 'Mark All',
])
<div x-data="switchAll({ group: @js($group) })" class="contents">
    <tk:switch
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldProps(), ['iconOn' => null, 'iconOff' => null, 'labelOn' => null, 'labelOff' => null, 'loadingDelay' => null, 'loadingMinDuration' => null])"
    />
</div>
