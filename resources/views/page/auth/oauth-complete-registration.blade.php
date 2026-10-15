@props([
    'size' => null,
    'provider' => null,
    'email' => null,
    'identifier' => null,
])
@php

$providerName = filled($provider) && $provider === strtolower($provider) ? TALLKit::oauthName($provider) : ($provider ?? '');

@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['identifier:', 'submit:', 'error:'])->merge(['title' => 'Complete your registration'])"
    :$size
    :subtitle="__('Signed in with :provider as :email. We just need a bit more information to finish creating your account.', [
        'provider' => $providerName,
        'email' => $email ?? '',
    ])"
>
    @error('oauth')
        <tk:alert :attributes="$attributes->prefixed('error:')" type="danger" :$message />
    @enderror

    <tk:page.auth.identifier
        :attributes="$attributes->prefixed('identifier:')"
        :$size
        :$identifier
    />

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')->merge(['label' => 'Create account'])"
        :$size
        variant="accent"
    />
</tk:form.section>
