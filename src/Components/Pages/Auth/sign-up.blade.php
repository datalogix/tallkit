<tk:formi.section
    :attributes="$attributes->whereDoesntStartWith(['name:', 'email:', 'password:', 'password-confirmation:', 'terms:', 'submit:', 'login:'])"
    title="Create an account"
    subtitle="Enter your details below to create your account:"
>
    <tk:input
        :attributes="$attributesAfter('name:')"
        name="name"
        required
        autocomplete="name"
        autofocus
        placeholder="Full name"
    />

    <tk:input
        :attributes="$attributesAfter('email:')"
        name="email"
        required
        autocomplete="email"
        placeholder="email@example.com"
    />

    <tk:password
        :attributes="$attributesAfter('password:')"
        name="password"
        required
        autocomplete="new-password"
        placeholder
    />

    <tk:password
        :attributes="$attributesAfter('password-confirmation:')"
        name="password_confirmation"
        required
        autocomplete="new-password"
        placeholder
    />

    <tk:terms.acceptance
        :attributes="$attributesAfter('terms:')"
        name="terms"
        required
        variant="accent"
        size="sm"
    />

    <tk:submit
        :attributes="$attributesAfter('submit:')->classes('w-full')"
        label="Create account"
        variant="accent"
    />

    @if ($login)
        <x-slot:append>
            <div {{ $attributesAfter('login:container:')->classes('space-x-1 rtl:space-x-reverse flex justify-center') }}>
                <tk:text
                    :attributes="$attributesAfter('login:label:')"
                    label="Already have an account?"
                />

                <tk:link
                    :attributes="$attributesAfter('login:link:')"
                    :href="$login"
                    label="Sign in"
                />
            </div>
        </x-slot:append>
    @endif
</tk:formi.section>
