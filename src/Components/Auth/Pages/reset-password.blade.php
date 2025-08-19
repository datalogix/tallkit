<tk:section
    :attributes="$attributes->whereDoesntStartWith(['form:', 'email:', 'new-password:', 'new-password-confirmation:', 'submit:'])"
    title="Reset password"
    subtitle="Please enter your new password below:"
>
    <tk:form :attributes="$attributesAfter('form:')">
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

        <tk:submit
            :attributes="$attributesAfter('submit:')->classes('w-full')"
            label="Reset password"
            variant="accent"
        />
    </tk:form>
</tk:section>
