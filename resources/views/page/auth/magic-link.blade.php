@props([
    'loginUrl' => null,
    'size' => null,
    'identifier' => null,
    'confirmEmail' => null,
])
@php

$loginUrl ??= route_detect([
    'login', 'auth.login',
    'signin', 'auth.signin',
    'sign-in', 'auth.sign-in',
], default: null);

@endphp
@if ($confirmEmail)
    <tk:form.section
        :attributes="$attributes->whereDoesntStartWith(['identifier:', 'submit:', 'login:', 'confirm:'])->merge(['title' => 'Sign in with a link'])"
        :$size
        :subtitle="__('Sign in as :email?', ['email' => $confirmEmail])"
        action="confirm"
    >
        {{ $slot }}

        <tk:submit
            :attributes="$attributes->prefixed('confirm:')->classes('w-full')->merge(['label' => 'Sign in'])"
            :$size
            variant="accent"
        />
    </tk:form.section>
@else
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['identifier:', 'submit:', 'login:'])->merge(['title' => 'Sign in with a link', 'subtitle' => 'We will e-mail you a link that signs you in, no password needed.'])"
    :$size
>
    {{ $slot }}

    <tk:page.auth.identifier
        :attributes="$attributes->prefixed('identifier:')"
        :$size
        :$identifier
    />

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')->merge(['label' => 'Send sign-in link'])"
        :$size
        variant="accent"
    />

    @if ($loginUrl)
        <tk:separator :attributes="$attributes->prefixed('login:separator:')" />

        <tk:link
            :attributes="$attributes->prefixed('login:link:')->merge(['label' => 'Sign in with password'])"
            :href="$loginUrl"
            :$size
        />
    @endif
</tk:form.section>
@endif
