<tk:section
    :attributes="$attributes->whereDoesntStartWith(['form:', 'email:', 'submit:', 'login:'])"
    title="Forgot password"
    subtitle="Enter your email address and we'll send you a password reset link."
>
    <tk:form :attributes="$attributesAfter('form:')">
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
    </tk:form>

    @if ($login)
        <tk:element
            :attributes="$attributesAfter('login:')->classes('hover:underline')"
            :href="$login"
            label="Back to sign in"
        />
    @endif
</tk:section>
