<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['email:', 'password:', 'forgot-password:', 'remember:', 'submit:', 'sign-up:'])"
    :$size
    title="Sign in to your account"
    subtitle="Enter your access details below to sign in:"
>
    <tk:input
        :attributes="$attributesAfter('email:')"
        :$size
        name="email"
        autofocus
        required
        autocomplete="email"
        placeholder="email@example.com"
    />

    <tk:password
        :attributes="$attributesAfter('password:')"
        :$size
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
                    :$size
                    label="Forgot your password?"
                    tabindex="-1"
                />
            </x-slot:label-append>
        @endif
    </tk:password>

    <tk:checkbox
        :attributes="$attributesAfter('remember:')"
        :$size
        name="remember"
        label="Remember me"
    />

    <tk:submit
        :attributes="$attributesAfter('submit:')->classes('w-full')"
        :$size
        label="Sign in"
        variant="accent"
    />

    @if ($signUp)
        <x-slot:append>
            <div {{ $attributesAfter('sign-up:container:')->classes('space-x-1 rtl:space-x-reverse flex justify-center') }}>
                <tk:text
                    :attributes="$attributesAfter('sign-up:label:')"
                    :$size
                    label="Don't have an account?"
                />

                <tk:link
                    :attributes="$attributesAfter('sign-up:link:')"
                    :href="$signUp"
                    :$size
                    label="Sign up"
                />
            </div>
        </x-slot:append>
    @endif
</tk:form.section>
