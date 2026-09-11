@props([
    'forgotPasswordUrl' => null,
    'signUpUrl' => null,
    'size' => null,
    'identifier' => null,
    'oauth' => null,
])
@php

$forgotPasswordUrl ??= route_detect([
    'forgot-password',
    'auth.forgot-password'
], default: null);

$signUpUrl ??= route_detect([
    'signup', 'auth.signup',
    'sign-up', 'auth.sign-up',
    'register', 'auth.register',
], default: null);

@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['identifier:', 'password:', 'forgot-password:', 'remember:', 'submit:', 'oauth:', 'sign-up:'])"
    :$size
    title="Sign in to your account"
    subtitle="Enter your access details below to sign in:"
>
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
        placeholder
    >
        @if ($forgotPasswordUrl)
            <x-slot:labelAppend>
                <tk:link
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'forgot-password:')"
                    :href="$forgotPasswordUrl"
                    :$size
                    label="Forgot your password?"
                />
            </x-slot:labelAppend>
        @endif
    </tk:password>

    <tk:checkbox
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'remember:')"
        :$size
        name="remember"
        label="Remember me"
    />

    <tk:submit
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'submit:')->classes('w-full')"
        :$size
        label="Sign in"
        variant="accent"
    />

    <tk:page.auth.oauth
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'oauth:')"
        :$size
        :providers="$oauth"
    />

    @if ($signUpUrl)
        <tk:separator :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'separator:')" />

        <div
            {{
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'sign-up:container:')
                    ->classes('space-x-1 rtl:space-x-reverse flex justify-center')
            }}
        >
            <tk:text
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'sign-up:label:')"
                :$size
                label="Don't have an account?"
            />

            <tk:link
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'sign-up:link:')"
                :href="$signUpUrl"
                :$size
                label="Sign up"
            />
        </div>
    @endif
</tk:form.section>
