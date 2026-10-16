@props([
    'loginUrl' => null,
    'size' => null,
    'identifier' => null,
    'requiresEmail' => null,
    'oauth' => null,
    'terms' => null,
])
@php

$loginUrl ??= route_detect([
    'login', 'auth.login',
    'signin', 'auth.signin',
    'sign-in', 'auth.sign-in',
], default: null);

@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['name:', 'email:', 'identifier:', 'password:', 'password-confirmation:', 'terms:', 'submit:', 'oauth:', 'login:'])"
    title="Create an account"
    subtitle="Enter your details below to create your account:"
    :$size
>
    <tk:input
        :attributes="$attributes->prefixed('name:')"
        placeholder="Full name"
        :$size
        name="name"
        required
        autocomplete="name"
        autofocus
    />

    @if ($requiresEmail !== false && ($identifier ?? 'email') !== 'email')
        <tk:input
            :attributes="$attributes->prefixed('email:')"
            placeholder="Email address"
            :$size
            name="email"
            required
            autocomplete="email"
        />
    @endif

    <tk:page.auth.identifier
        :attributes="$attributes->prefixed('identifier:')"
        :$size
        :$identifier
    />

    <tk:password
        :attributes="$attributes->prefixed('password:')"
        placeholder
        :$size
        name="password"
        required
        autocomplete="new-password"
    />

    <tk:password
        :attributes="$attributes->prefixed('password-confirmation:')"
        placeholder
        :$size
        name="password_confirmation"
        required
        autocomplete="new-password"
    />

    {{ $slot }}

    @if ($terms !== false)
        <tk:terms.acceptance
            :attributes="$attributes->prefixed('terms:')"
            :$size
            :terms-of-service="string_value($terms)"
            name="terms"
            required
            variant="accent"
        />
    @endif

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')"
        label="Create account"
        :$size
        variant="accent"
    />

    <tk:page.auth.oauth
        :attributes="$attributes->prefixed('oauth:')"
        :$size
        :items="$oauth"
    />

    @if ($loginUrl)
        <tk:separator :attributes="$attributes->prefixed('login:separator:')" />

        <div
            {{
                $attributes->prefixed('login:container:')
                    ->classes('space-x-1 flex justify-center')
            }}
        >
            <tk:text
                :attributes="$attributes->prefixed('login:label:')"
                label="Already have an account?"
                :$size
            />

            <tk:link
                :attributes="$attributes->prefixed('login:link:')"
                label="Sign in"
                :$size
                :href="$loginUrl"
            />
        </div>
    @endif
</tk:form.section>
