@props([
    'login' => null,
    'size' => null,
    'identifier' => null
])
@php

$login ??= route_detect([
    'login', 'auth.login',
    'signin', 'auth.signin',
    'sign-in', 'auth.sign-in',
], default: null);

@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['identifier:', 'submit:', 'separator:', 'login:'])"
    :$size
    title="Forgot password"
    :subtitle="match ($identifier) {
        'cpf' => 'Enter your CPF and we\'ll send you a password reset link.',
        'username' => 'Enter your username and we\'ll send you a password reset link.',
        'both' => 'Enter your login and we\'ll send you a password reset link.',
        default => 'Enter your email address and we\'ll send you a password reset link.',
    }"
>
    {{ $slot }}

    <tk:page.auth.identifier
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'identifier:')"
        :$size
        :$identifier
    />

    <tk:submit
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'submit:')->classes('w-full')"
        :$size
        label="Send password reset link"
        variant="accent"
    />

    @if ($login)
        <tk:separator :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'separator:')" />

        <tk:link
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'login:')"
            :href="$login"
            :$size
            label="Back to sign in"
        />
    @endif
</tk:form.section>
