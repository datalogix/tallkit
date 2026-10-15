@props([
    'size' => null,
    'email' => null,
    'status' => null,
    'loginUrl' => null,
])
@php

$loginUrl ??= route_detect(['login', 'auth.login'], default: null);

@endphp
@if ($status === 'expired')
    <tk:form.section
        :attributes="$attributes->whereDoesntStartWith(['resend:'])->merge(['title' => 'This invitation has expired'])"
        :$size
        :subtitle="__('We can send a new link to :email.', ['email' => $email])"
        action="resend"
    >
        {{ $slot }}

        <tk:submit
            :attributes="$attributes->prefixed('resend:')->classes('w-full')->merge(['label' => 'Send me a new link'])"
            :$size
            variant="accent"
        />
    </tk:form.section>
@elseif ($status)
    <tk:section
        :attributes="$attributes->whereDoesntStartWith(['login:'])->merge([
            'title' => $status === 'used' ? 'This invitation was already accepted' : 'This invitation link is not valid',
            'subtitle' => $status === 'used' ? 'Sign in with the password you chose.' : 'Ask for another invitation.',
            'separator' => false,
        ])"
    >
        {{ $slot }}

        @if ($status === 'used' && $loginUrl)
            <tk:button
                :attributes="$attributes->prefixed('login:')->classes('w-full')->merge(['label' => 'Sign in'])"
                :$size
                :href="$loginUrl"
                variant="accent"
            />
        @endif
    </tk:section>
@else
    <tk:form.section
        :attributes="$attributes->whereDoesntStartWith(['new-password:', 'new-password-confirmation:', 'submit:'])->merge(['title' => 'Welcome'])"
        :$size
        :subtitle="$email ? __('Choose a password for :email to start using your account.', ['email' => $email]) : __('Choose a password to start using your account.')"
    >
        {{ $slot }}

        <tk:password
            :attributes="$attributes->prefixed('new-password:')->merge(['placeholder' => true])->merge(['label' => 'New password'])"
            :$size
            name="password"
            required
            autocomplete="new-password"
        />

        <tk:password
            :attributes="$attributes->prefixed('new-password-confirmation:')->merge(['placeholder' => true])->merge(['label' => 'New password confirmation'])"
            :$size
            name="password_confirmation"
            required
            autocomplete="new-password"
        />

        <tk:submit
            :attributes="$attributes->prefixed('submit:')->classes('w-full')->merge(['label' => 'Choose my password'])"
            :$size
            variant="accent"
        />
    </tk:form.section>
@endif
