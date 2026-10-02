@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'controls' => null,
])
<tk:input
    :attributes="TALLKit::fieldWithProps($attributes, get_defined_vars())
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
