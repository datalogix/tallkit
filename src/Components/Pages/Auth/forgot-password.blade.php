<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['email:', 'submit:', 'login:'])"
    title="Forgot password"
    subtitle="Enter your email address and we'll send you a password reset link."
>
    <tk:input
        :attributes="$attributesAfter('email:')"
        name="email"
        required
        autofocus
        placeholder="email@example.com"
    />

    <tk:submit
        :attributes="$attributesAfter('submit:')->classes('w-full')"
        label="Send password reset link"
        variant="accent"
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


