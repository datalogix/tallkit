@props([
    'size' => null,
    'provider' => null,
    'email' => null,
])
@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['identifier:', 'submit:'])"
    :$size
    title="Complete your registration"
    :subtitle="__('Signed in with :provider as :email. We just need a bit more information to finish creating your account.', [
        'provider' => str($provider ?? '')->headline(),
        'email' => $email ?? '',
    ])"
>
    <tk:page.auth.identifier
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'identifier:')"
        :$size
        :$identifier
    />

    <tk:submit
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'submit:')->classes('w-full')"
        :$size
        label="Create account"
        variant="accent"
    />
</tk:form.section>
