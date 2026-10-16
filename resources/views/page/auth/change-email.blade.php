@props([
    'size' => null,
    'currentEmail' => null,
])
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['current:', 'email:', 'submit:'])"
    title="Change e-mail"
    subtitle="We will send a link to the new address. Your e-mail changes once you open it."
    :$size
>
    {{ $slot }}

    @if ($currentEmail)
        <tk:text
            :attributes="$attributes->prefixed('current:')"
            :$size
            :label="__('Current e-mail: :email', ['email' => $currentEmail])"
        />
    @endif

    <tk:input
        :attributes="$attributes->prefixed('email:')->except('type')"
        placeholder
        label="New e-mail"
        :$size
        type="email"
        name="email"
        required
        autocomplete="email"
    />

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')"
        label="Send link"
        :$size
        variant="accent"
    />
</tk:form.section>
