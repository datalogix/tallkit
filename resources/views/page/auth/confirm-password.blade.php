@props([
    'size' => null,
])
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['password:', 'submit:'])->merge(['title' => 'Confirm password', 'subtitle' => 'This is a secure area of the application. Please confirm your password before continuing.'])"
    :$size
>
    {{ $slot }}

    <tk:password
        :attributes="$attributes->prefixed('password:')->merge(['placeholder' => true])"
        :$size
        name="password"
        required
        autocomplete="current-password"
    />

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')->merge(['label' => 'Confirm'])"
        :$size
        variant="accent"
    />
</tk:form.section>
