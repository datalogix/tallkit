@props([
    'viewable' => true,
])
<tk:input
    :attributes="TALLKit::mergeDefinedFieldProps($attributes, get_defined_vars())"
    type="password"
    name="password"
    placeholder
    :$viewable
/>
