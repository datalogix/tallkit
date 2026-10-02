@props([
    'loginUrl' => null,
    'size' => null,
    'identifier' => null
])
@php

$loginUrl ??= route_detect([
    'login', 'auth.login',
    'signin', 'auth.signin',
    'sign-in', 'auth.sign-in',
], default: null);

@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['identifier:', 'submit:', 'login:'])->merge(['title' => 'Forgot password'])"
    :$size
    :subtitle="match ($identifier) {
        'cpf' => 'Enter your CPF and we\'ll send you a password reset link.',
        'cnpj' => 'Enter your CNPJ and we\'ll send you a password reset link.',
        'username' => 'Enter your username and we\'ll send you a password reset link.',
        'login' => 'Enter your login and we\'ll send you a password reset link.',
        default => 'Enter your email address and we\'ll send you a password reset link.',
    }"
>
    {{ $slot }}

    <tk:page.auth.identifier
        :attributes="$attributes->prefixed('identifier:')"
        :$size
        :$identifier
    />

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')->merge(['label' => 'Send password reset link'])"
        :$size
        variant="accent"
    />

    @if ($loginUrl)
        <tk:separator :attributes="$attributes->prefixed('login:separator:')" />

        <tk:link
            :attributes="$attributes->prefixed('login:link:')->merge(['label' => 'Back to sign in'])"
            :href="$loginUrl"
            :$size
        />
    @endif
</tk:form.section>
