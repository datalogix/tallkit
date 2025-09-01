<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['email:', 'password:', 'forgot-password:', 'remember:', 'sign-up:'])"
    title="Sign in to your account"
    subtitle="Enter your access details below to sign in:"
    submit:label="Sign in"
    submit:variant="accent"
>
    <tk:input
        :attributes="$attributesAfter('email:')"
        name="email"
        autofocus
        required
        autocomplete="email"
        placeholder="email@example.com"
    />

    <tk:input
        :attributes="$attributesAfter('password:')"
        name="password"
        required
        autocomplete="current-password"
        placeholder
    >
        @if ($forgotPassword)
            <x-slot:label-append>
                <tk:link
                    :attributes="$attributesAfter('forgot-password:')"
                    :href="$forgotPassword"
                    label="Forgot your password?"
                />

            </x-slot:label-append>
        @endif
    </tk:input>

    <tk:checkbox
        :attributes="$attributesAfter('remember:')"
        name="remember"
        label="Remember me"
    />

    @if ($signUp)
        <x-slot:append>
            <div {{ $attributesAfter('sign-up:container:')->classes('space-x-1 rtl:space-x-reverse flex justify-center') }}>
                <tk:text
                    :attributes="$attributesAfter('sign-up:label:')"
                    label="Don't have an account?"
                />

                <tk:link
                    :attributes="$attributesAfter('sign-up:link:')"
                    :href="$signUp"
                    label="Sign up"
                />
            </div>
        </x-slot:append>
    @endif
</tk:form.section>
