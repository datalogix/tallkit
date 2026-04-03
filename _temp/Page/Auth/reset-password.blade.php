<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['email:', 'new-password:', 'new-password-confirmation:', 'submit:'])"
    :$size
    title="Reset password"
    subtitle="Please enter your new password below:"
>
    <tk:input
        :attributes="$attributesAfter('email:')"
        :$size
        name="email"
        required
        autocomplete="email"
    />

    <tk:password
        :attributes="$attributesAfter('new-password:')"
        :$size
        label="New password"
        name="password"
        required
        autocomplete="new-password"
        placeholder
    />

    <tk:password
        :attributes="$attributesAfter('new-password-confirmation:')"
        :$size
        label="New password confirmation"
        name="password_confirmation"
        required
        autocomplete="new-password"
        placeholder
    />

    <tk:submit
        :attributes="$attributesAfter('submit:')->classes('w-full')"
        :$size
        label="Reset password"
        variant="accent"
    />
</tk:form.section>
