<tk:section
    :attributes="$attributes->whereDoesntStartWith(['form:', 'name:', 'email:', 'password:', 'password-confirmation:', 'terms:', 'submit:', 'login:'])"
    title="Create an account"
    subtitle="Enter your details below to create your account:"
>
    <tk:form :attributes="$attributesAfter('form:')">
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

        {{ $slot }}

        <tk:input
            :attributes="$attributesAfter('password:')"
            name="password"
            required
            autocomplete="new-password"
            placeholder
        />

        <tk:input
            :attributes="$attributesAfter('password-confirmation:')"
            name="password_confirmation"
            required
            autocomplete="new-password"
            placeholder
        />

        @if (route_detect(['terms-of-service', 'terms'], default: null))
            <tk:toggle
                :attributes="$attributesAfter('terms:')"
                name="terms"
                required
                variant="accent"
                label:class="font-normal text-sm"
            >
                {!! $terms ?? __('I agree to the :terms-of-service', ['terms-of-service' =>
                    '<a href="'.route_detect(['terms-of-service', 'terms']).'" target="_blank">'.__('Terms of Service').'</a>',
                ]) !!}
            </tk:toggle>
        @endif

        <tk:submit
            :attributes="$attributesAfter('submit:')->classes('w-full')"
            label="Create account"
            variant="accent"
        />
    </tk:form>

    @if ($login)
        <div {{ $attributesAfter('login:container:')->classes('space-x-1 rtl:space-x-reverse flex justify-center') }}>
            <tk:element
                :attributes="$attributesAfter('login:label:')"
                label="Already have an account?"
            />

            <tk:element
                :attributes="$attributesAfter('login:link:')->classes('hover:underline')"
                :href="$login"
                label="Sign in"
             />
        </div>
    @endif
</tk:section>
