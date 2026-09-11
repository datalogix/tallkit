@props([
    'size' => null,
    'token' => null,
    'identifier' => null,
    'identifierValue' => null,
])
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['token:', 'identifier:', 'new-password:', 'new-password-confirmation:', 'submit:'])"
    :$size
    title="Reset password"
    subtitle="Please enter your new password below:"
>
    <input
        type="hidden"
        name="token"
        {{
            TALLKit::attributesAfter(attributes: $attributes, prefix: 'token:')
                ->when(
                    in_livewire(),
                    fn ($attrs) => $attrs->merge(['wire:model' => 'token']),
                    fn ($attrs) => $attrs->merge(['value' => $token ?? request()->route('token') ?? request('token')]),
                )
        }}
    />

    {{ $slot }}

    <tk:page.auth.identifier
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'identifier:')"
        :$size
        :$identifier
        :value="$identifierValue ?? old(match ($identifier) {
            'cpf' => 'cpf',
            'username' => 'username',
            'both' => 'login',
            default => 'email',
        }, request(match ($identifier) {
            'cpf' => 'cpf',
            'username' => 'username',
            'both' => 'login',
            default => 'email',
        }))"
    />

    <tk:password
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'new-password:')"
        :$size
        label="New password"
        name="password"
        required
        autocomplete="new-password"
        placeholder
    />

    <tk:password
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'new-password-confirmation:')"
        :$size
        label="New password confirmation"
        name="password_confirmation"
        required
        autocomplete="new-password"
        placeholder
    />

    <tk:submit
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'submit:')->classes('w-full')"
        :$size
        label="Reset password"
        variant="accent"
    />
</tk:form.section>
