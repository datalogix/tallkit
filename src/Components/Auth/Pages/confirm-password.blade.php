<tk:section
    :attributes="$attributes->whereDoesntStartWith(['form:', 'password:', 'submit:'])"
    title="Confirm password"
    subtitle="This is a secure area of the application. Please confirm your password before continuing."
>
    <tk:form :attributes="$attributesAfter('form:')">
        <tk:input
            :attributes="$attributesAfter('password:')"
            name="password"
            required
            autocomplete="new-password"
            placeholder
        />

        <tk:submit
            :attributes="$attributesAfter('submit:')->classes('w-full')"
            label="Confirm"
            variant="accent"
        />
    </tk:form>
</tk:section>
