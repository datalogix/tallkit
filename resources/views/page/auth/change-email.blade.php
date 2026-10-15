@props([
    'size' => null,
    'currentEmail' => null,
])
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['current:', 'email:', 'submit:'])->merge(['title' => 'Change e-mail', 'subtitle' => 'We will send a link to the new address. Your e-mail changes once you open it.'])"
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
        :attributes="$attributes->prefixed('email:')->merge(['placeholder' => true])->merge(['label' => 'New e-mail'])"
        :$size
        type="email"
        name="email"
        required
        autocomplete="email"
    />

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')->merge(['label' => 'Send link'])"
        :$size
        variant="accent"
    />
</tk:form.section>
