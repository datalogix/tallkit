@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'controls' => null,
])
<tk:input
    :attributes="$attributes->withFieldProps(get_defined_vars(), TALLKit::fieldControlProps())
        ->merge(array_filter([
            'role' => 'combobox',
            'aria-autocomplete' => 'list',
            'aria-expanded' => 'true',
            'aria-controls' => $controls,
            'aria-label' => __('Search'),
        ]))
        ->merge(['placeholder' => 'Search...'])
    "
    :label="false"
    type="search"
    icon="search"
    clearable
    autocomplete="off"
/>
