<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['email:', 'new-password:', 'new-password-confirmation:'])"
    title="Reset password"
    subtitle="Please enter your new password below:"
    submit:label="Reset password"
    submit:variant="accent"
>
    <tk:input
        :attributes="$attributesAfter('email:')"
        name="email"
        required
        autocomplete="email"
    />

    <tk:input
        :attributes="$attributesAfter('new-password:')"
        label="New password"
        name="password"
        required
        autocomplete="new-password"
        placeholder
    />

    <tk:input
        :attributes="$attributesAfter('new-password-confirmation:')"
        label="New password confirmation"
        name="password_confirmation"
        required
        autocomplete="new-password"
        placeholder
    />
</tk:form.section>
