@props([
    'loginUrl' => null,
    'size' => null,
    'identifier' => null,
    'confirmEmail' => null,
    'mode' => 'link',
    'codeStep' => false,
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
        :attributes="$attributes->whereDoesntStartWith(['identifier:', 'submit:', 'login:', 'confirm:', 'code:', 'verify:', 'back:'])"
        title="Sign in with a link"
        :$size
        :subtitle="__('Sign in as :email?', ['email' => $confirmEmail])"
        action="confirm"
    >
        {{ $slot }}

        <tk:submit
            :attributes="$attributes->prefixed('confirm:')->classes('w-full')"
            label="Sign in"
            :$size
            variant="accent"
        />
    </tk:form.section>
@elseif ($codeStep)
    <tk:form.section
        :attributes="$attributes->whereDoesntStartWith(['identifier:', 'submit:', 'login:', 'confirm:', 'code:', 'verify:', 'back:'])"
        title="Enter the code"
        :subtitle="$mode === 'both'
            ? 'Enter the 6-digit code we e-mailed you, or open the link in the e-mail.'
            : 'Enter the 6-digit code we e-mailed you.'"
        :$size
        action="verifyCode"
    >
        {{ $slot }}

        <tk:otp
            :attributes="$attributes->prefixed('code:')"
            label="Sign-in code"
            :$size
            name="code"
        />

        <tk:submit
            :attributes="$attributes->prefixed('verify:')->classes('w-full')"
            label="Sign in"
            :$size
            variant="accent"
        />

        @if (in_livewire())
            <tk:button
                :attributes="$attributes->prefixed('back:')->except('type')->classes('w-full')"
                label="Use another login"
                :$size
                variant="ghost"
                action="cancelCode"
            />
        @endif
    </tk:form.section>
@else
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['identifier:', 'submit:', 'login:', 'code:', 'verify:', 'back:'])"
    :title="$mode === 'code' ? 'Sign in with a code' : 'Sign in with a link'"
    :subtitle="match ($mode) {
        'code' => 'We will e-mail you a code that signs you in, no password needed.',
        'both' => 'We will e-mail you a link and a code that sign you in, no password needed.',
        default => 'We will e-mail you a link that signs you in, no password needed.',
    }"
    :$size
>
    {{ $slot }}

    <tk:page.auth.identifier
        :attributes="$attributes->prefixed('identifier:')"
        :$size
        :$identifier
    />

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')"
        :label="$mode === 'code' ? 'Send sign-in code' : 'Send sign-in link'"
        :$size
        variant="accent"
    />

    @if ($loginUrl)
        <tk:separator :attributes="$attributes->prefixed('login:separator:')" />

        <tk:link
            :attributes="$attributes->prefixed('login:link:')"
            label="Sign in with password"
            :href="$loginUrl"
            :$size
        />
    @endif
</tk:form.section>
@endif
