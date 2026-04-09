@props([
    'forgotPassword' => null,
    'signUp' => null,
    'size' => null,
])
@php

$forgotPassword ??= route_detect([
    'forgot-password',
    'auth.forgot-password'
], default: null);

$signUp ??= route_detect([
    'signup', 'auth.signup',
    'sign-up', 'auth.sign-up',
    'register', 'auth.register',
], default: null);

@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['email:', 'password:', 'forgot-password:', 'remember:', 'submit:', 'sign-up:'])"
    :$size
    title="Sign in to your account"
    subtitle="Enter your access details below to sign in:"
>
    <tk:input
        :attributes="TALLKit::attributesAfter($attributes, 'email:')"
        :$size
        name="email"
        autofocus
        required
        autocomplete="email"
        placeholder="email@example.com"
    />

    <tk:password
        :attributes="TALLKit::attributesAfter($attributes, 'password:')"
        :$size
        name="password"
        required
        autocomplete="current-password"
        placeholder
    >
        @if ($forgotPassword)
            <x-slot:labelAppend>
                <tk:link-
                    :attributes="TALLKit::attributesAfter($attributes, 'forgot-password:')"
                    :href="$forgotPassword"
                    :$size
                    label="Forgot your password?"
                    tabindex="-1"
                />
            </x-slot:labelAppend>
        @endif
    </tk:password>

    <tk:checkbox
        :attributes="TALLKit::attributesAfter($attributes, 'remember:')"
        :$size
        name="remember"
        label="Remember me"
    />

    <tk:submit
        :attributes="TALLKit::attributesAfter($attributes, 'submit:')->classes('w-full')"
        :$size
        label="Sign in"
        variant="accent"
    />

    @if ($signUp)
        <x-slot:append>
            <div {{ TALLKit::attributesAfter($attributes, 'sign-up:container:')->classes('space-x-1 rtl:space-x-reverse flex justify-center') }}>
                <tk:text
                    :attributes="TALLKit::attributesAfter($attributes, 'sign-up:label:')"
                    :$size
                    label="Don't have an account?"
                />

                <tk:link
                    :attributes="TALLKit::attributesAfter($attributes, 'sign-up:link:')"
                    :href="$signUp"
                    :$size
                    label="Sign up"
                />
            </div>
        </x-slot:append>
    @endif
</tk:form.section>
