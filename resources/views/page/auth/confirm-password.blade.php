@props([
    'size' => null,
    'oauth' => [],
])
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['password:', 'submit:', 'oauth:'])"
    title="Confirm password"
    subtitle="This is a secure area of the application. Please confirm your password before continuing."
    :$size
>
    {{ $slot }}

    <tk:password
        :attributes="$attributes->prefixed('password:')"
        placeholder
        :$size
        name="password"
        required
        autocomplete="current-password"
    />

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')"
        label="Confirm"
        :$size
        variant="accent"
    />

    <tk:page.auth.oauth
        :attributes="$attributes->prefixed('oauth:')"
        label="Confirm with :provider"
        :$size
        :items="$oauth"
    />
</tk:form.section>
