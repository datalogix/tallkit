<tk:input
    :attributes="$attributes->withFieldProps(get_defined_vars(), TALLKit::fieldControlProps())->except('type')"
    placeholder
    type="password"
    name="password"
    autocomplete="current-password"
/>
