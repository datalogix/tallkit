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
    :attributes="$attributes->whereDoesntStartWith(['email:', 'submit:', 'login:'])"
    :$size
    title="Forgot password"
    subtitle="Enter your email address and we'll send you a password reset link."
>
    <tk:input
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'email:')"
        :$size
        name="email"
        required
        autofocus
        placeholder="email@example.com"
    />

    <tk:submit
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'submit:')->classes('w-full')"
        :$size
        label="Send password reset link"
        variant="accent"
    />

    @if ($login)
        <x-slot:append>
            <tk:link
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'login:')"
                :href="$login"
                :$size
                label="Back to sign in"
            />
        </x-slot:append>
    @endif
</tk:form.section>


