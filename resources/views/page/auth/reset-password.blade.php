@props([
    'size' => null,
    'token' => null,
    'identifier' => null,
    'identifierValue' => null,
])
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['token:', 'identifier:', 'new-password:', 'new-password-confirmation:', 'submit:'])->merge(['title' => 'Reset password', 'subtitle' => 'Please enter your new password below:'])"
    :$size
>
    <input
        type="hidden"
        name="token"
        {{
            $attributes->prefixed('token:')
                ->when(
                    in_livewire(),
                    fn ($attrs) => $attrs->merge(['wire:model' => 'token']),
                    fn ($attrs) => $attrs->merge(['value' => $token ?? request()->route('token') ?? request('token')]),
                )
        }}
    />

    {{ $slot }}

    <tk:page.auth.identifier
        :attributes="$attributes->prefixed('identifier:')"
        :$size
        :$identifier
        :value="$identifierValue ?? old($identifier ?? 'email', request($identifier ?? 'email'))"
    />

    <tk:password
        :attributes="$attributes->prefixed('new-password:')->merge(['placeholder' => true])->merge(['label' => 'New password'])"
        :$size
        name="password"
        required
        autocomplete="new-password"
    />

    <tk:password
        :attributes="$attributes->prefixed('new-password-confirmation:')->merge(['placeholder' => true])->merge(['label' => 'New password confirmation'])"
        :$size
        name="password_confirmation"
        required
        autocomplete="new-password"
    />

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')->merge(['label' => 'Reset password'])"
        :$size
        variant="accent"
    />
</tk:form.section>
