@props([
    'size' => null,
    'provider' => null,
    'email' => null,
    'identifier' => null,
])
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['identifier:', 'submit:'])->merge(['title' => 'Complete your registration'])"
    :$size
    :subtitle="__('Signed in with :provider as :email. We just need a bit more information to finish creating your account.', [
        'provider' => str($provider ?? '')->headline(),
        'email' => $email ?? '',
    ])"
>
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
