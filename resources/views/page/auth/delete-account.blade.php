@props([
    'size' => null,
    'confirmation' => null,
])
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['warning:', 'confirmation:', 'submit:'])->merge(['title' => 'Delete account', 'subtitle' => 'Your account and its data will be deleted for good. This cannot be undone.'])"
    :$size
>
    {{ $slot }}

    <tk:alert
        :attributes="$attributes->prefixed('warning:')->merge(['message' => 'You will be signed out, and nobody will be able to sign in to this account again.'])"
        type="danger"
    />

    <tk:input
        :attributes="$attributes->prefixed('confirmation:')->merge(['label' => __('Type :value to confirm', ['value' => $confirmation])])"
        :$size
        name="confirmation"
        required
        autocomplete="off"
    />

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')->merge(['label' => 'Delete account'])"
        :$size
        variant="danger"
    />
</tk:form.section>
