<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['email:', 'login:'])"
    title="Forgot password"
    subtitle="Enter your email address and we'll send you a password reset link."
    submit:label="Send password reset link"
    submit:variant="accent"
>
    <tk:input
        :attributes="$attributesAfter('email:')"
        name="email"
        required
        autofocus
        placeholder="email@example.com"
    />

    @if ($login)
        <x-slot:append>
            <tk:link
                :attributes="$attributesAfter('login:')"
                :href="$login"
                label="Back to sign in"
            />
        </x-slot:append>
    @endif
</tk:form.section>


