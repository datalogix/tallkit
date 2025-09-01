<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['password:'])"
    title="Confirm password"
    subtitle="This is a secure area of the application. Please confirm your password before continuing."
    submit:label="Confirm"
    submit:variant="accent"
>
    <tk:input
        :attributes="$attributesAfter('password:')"
        name="password"
        required
        autocomplete="new-password"
        placeholder
    />
</tk:form.section>
