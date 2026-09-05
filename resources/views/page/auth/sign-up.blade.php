@props([
    'login' => null,
    'size' => null,
])
@php

$login ??= route_detect([
    'login', 'auth.login',
    'signin', 'auth.signin',
    'sign-in', 'auth.sign-in',
], default: null);

@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['name:', 'email:', 'password:', 'password-confirmation:', 'terms:', 'submit:', 'login:'])"
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

    <tk:input
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'email:')"
        :$size
        name="email"
        required
        autocomplete="email"
        placeholder="email@example.com"
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

    @if ($login)
        <x-slot:append>
            <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'login:container:')->classes('space-x-1 rtl:space-x-reverse flex justify-center') }}>
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
        </x-slot:append>
    @endif
</tk:form.section>
