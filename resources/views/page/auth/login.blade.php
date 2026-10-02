@props([
    'forgotPasswordUrl' => null,
    'signUpUrl' => null,
    'size' => null,
    'identifier' => null,
    'oauth' => null,
])
@php

$forgotPasswordUrl ??= route_detect([
    'forgot-password',
    'auth.forgot-password'
], default: null);

$signUpUrl ??= route_detect([
    'signup', 'auth.signup',
    'sign-up', 'auth.sign-up',
    'register', 'auth.register',
], default: null);

@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['identifier:', 'password:', 'forgot-password:', 'remember:', 'submit:', 'oauth:', 'sign-up:'])->merge(['title' => 'Sign in to your account', 'subtitle' => 'Enter your access details below to sign in:'])"
    :$size
>
    {{ $slot }}

    <tk:page.auth.identifier
        :attributes="$attributes->prefixed('identifier:')"
        :$size
        :$identifier
    />

    <tk:password
        :attributes="$attributes->prefixed('password:')->merge(['placeholder' => true])"
        :$size
        name="password"
        required
    >
        @if ($forgotPasswordUrl)
            <x-slot:labelAppend>
                <tk:link
                    :attributes="$attributes->prefixed('forgot-password:')->merge(['label' => 'Forgot your password?'])"
                    :href="$forgotPasswordUrl"
                    :$size
                />
            </x-slot:labelAppend>
        @endif
    </tk:password>

    <tk:checkbox
        :attributes="$attributes->prefixed('remember:')->merge(['label' => 'Remember me'])"
        :$size
        name="remember"
    />

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')->merge(['label' => 'Sign in'])"
        :$size
        variant="accent"
    />

    <tk:page.auth.oauth
        :attributes="$attributes->prefixed('oauth:')"
        :$size
        :items="$oauth"
    />

    @if ($signUpUrl)
        <tk:separator :attributes="$attributes->prefixed('sign-up:separator:')" />

        <div
            {{
                $attributes->prefixed('sign-up:container:')
                    ->classes('space-x-1 flex justify-center')
            }}
        >
            <tk:text
                :attributes="$attributes->prefixed('sign-up:label:')->merge(['label' => 'Don\'t have an account?'])"
                :$size
            />

            <tk:link
                :attributes="$attributes->prefixed('sign-up:link:')->merge(['label' => 'Sign up'])"
                :href="$signUpUrl"
                :$size
            />
        </div>
    @endif
</tk:form.section>
