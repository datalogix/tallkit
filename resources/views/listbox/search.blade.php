@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'controls' => null,
])
<tk:input
    :attributes="$attributes->withFieldProps(get_defined_vars(), TALLKit::fieldControlProps())->except(['label', 'type'])"
    :aria-controls="$controls"
    role="combobox"
    aria-autocomplete="list"
    aria-expanded="true"
    :aria-label="__('Search')"
    placeholder="Search..."
    :label="false"
    type="search"
    icon="search"
    clearable
    autocomplete="off"
/>
