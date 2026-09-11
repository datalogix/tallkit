@props([
    'login' => null,
    'size' => null,
    'identifier' => null,
    'oauth' => null,
])
@php

$login ??= route_detect([
    'login', 'auth.login',
    'signin', 'auth.signin',
    'sign-in', 'auth.sign-in',
], default: null);

@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['name:', 'identifier:', 'password:', 'password-confirmation:', 'terms:', 'submit:', 'oauth:', 'login:'])"
    :$size
    title="Create an account"
    subtitle="Enter your details below to create your account:"
>
    <tk:input
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'name:')"
        :$size
        name="name"
        required
        autocomplete="name"
        autofocus
        placeholder="Full name"
    />

    {{ $slot }}

    <tk:page.auth.identifier
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'identifier:')"
        :$size
        :$identifier
    />

    <tk:password
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'password:')"
        :$size
        name="password"
        required
        autocomplete="new-password"
        placeholder
    />

    <tk:password
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'password-confirmation:')"
        :$size
        name="password_confirmation"
        required
        autocomplete="new-password"
        placeholder
    />

    <tk:terms.acceptance
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'terms:')"
        :$size
        name="terms"
        required
        variant="accent"
    />

    <tk:submit
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'submit:')->classes('w-full')"
        :$size
        label="Create account"
        variant="accent"
    />

    <tk:page.auth.oauth
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'oauth:')"
        :$size
        :providers="$oauth"
    />

    @if ($login)
        <tk:separator :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'separator:')" />

        <div
            {{
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'login:container:')
                    ->classes('space-x-1 rtl:space-x-reverse flex justify-center')
            }}
        >
            <tk:text
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'login:label:')"
                :$size
                label="Already have an account?"
            />

            <tk:link
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'login:link:')"
                :$size
                :href="$login"
                label="Sign in"
            />
        </div>
    @endif
</tk:form.section>
