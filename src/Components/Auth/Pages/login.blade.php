<tk:section
    :attributes="$attributes->whereDoesntStartWith(['form:', 'email:', 'password:', 'forgot-password:', 'remember:', 'submit:', 'sign-up:'])"
    title="Sign in to your account"
    subtitle="Enter your access details below to sign in:"
>
    <tk:form :attributes="$attributesAfter('form:')">
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
                    <tk:element
                        :attributes="$attributesAfter('forgot-password:')->classes('hover:underline text-sm')"
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

        <tk:submit
            :attributes="$attributesAfter('submit:')->classes('w-full')"
            label="Sign in"
            variant="accent"
        />
    </tk:form>

    @if ($signUp)
        <div {{ $attributesAfter('sign-up:container:')->classes('space-x-1 rtl:space-x-reverse flex justify-center') }}>
            <tk:element
                :attributes="$attributesAfter('sign-up:label:')"
                label="Don't have an account?"
            />

            <tk:element
                :attributes="$attributesAfter('sign-up:link:')->classes('hover:underline')"
                :href="$signUp"
                label="Sign up"
            />
        </div>
    @endif
</tk:section>
