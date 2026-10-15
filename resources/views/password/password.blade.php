<tk:input
    :attributes="$attributes->withFieldProps(get_defined_vars(), TALLKit::fieldControlProps())->merge(['placeholder' => true])"
    type="password"
    name="password"
    autocomplete="current-password"
/>
