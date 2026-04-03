<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['name:', 'email:', 'password:', 'password-confirmation:', 'terms:', 'submit:', 'login:'])"
    :$size
    title="Create an account"
    subtitle="Enter your details below to create your account:"
>
    <tk:input
        :attributes="$attributesAfter('name:')"
        :$size
        name="name"
        required
        autocomplete="name"
        autofocus
        placeholder="Full name"
    />

    <tk:input
        :attributes="$attributesAfter('email:')"
        :$size
        name="email"
        required
        autocomplete="email"
        placeholder="email@example.com"
    />

    <tk:password
        :attributes="$attributesAfter('password:')"
        :$size
        name="password"
        required
        autocomplete="new-password"
        placeholder
    />

    <tk:password
        :attributes="$attributesAfter('password-confirmation:')"
        :$size
        name="password_confirmation"
        required
        autocomplete="new-password"
        placeholder
    />

    <tk:terms.acceptance
        :attributes="$attributesAfter('terms:')"
        :$size
        name="terms"
        required
        variant="accent"
    />

    <tk:submit
        :attributes="$attributesAfter('submit:')->classes('w-full')"
        :$size
        label="Create account"
        variant="accent"
    />

    @if ($login)
        <x-slot:append>
            <div {{ $attributesAfter('login:container:')->classes('space-x-1 rtl:space-x-reverse flex justify-center') }}>
                <tk:text
                    :attributes="$attributesAfter('login:label:')"
                    :$size
                    label="Already have an account?"
                />

                <tk:link
                    :attributes="$attributesAfter('login:link:')"
                    :$size
                    :href="$login"
                    label="Sign in"
                />
            </div>
        </x-slot:append>
    @endif
</tk:form.section>
