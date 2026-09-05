@props([
    'size' => null,
])
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['password:', 'submit:'])"
    :$size
    title="Confirm password"
    subtitle="This is a secure area of the application. Please confirm your password before continuing."
>
    <tk:password
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'password:')"
        :$size
        name="password"
        required
        autocomplete="new-password"
        placeholder
    />

    <tk:submit
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'submit:')->classes('w-full')"
        :$size
        label="Confirm"
        variant="accent"
    />
</tk:form.section>
