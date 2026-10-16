@props([
    'currentPassword' => false,
    'logoutUrl' => null,
    'reason' => null,
    'size' => null,
])
@php

$logoutUrl ??= route_detect(['logout', 'auth.logout'], default: null);

@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['current-password:', 'new-password:', 'new-password-confirmation:', 'submit:', 'logout:'])"
    title="Change your password"
    :subtitle="$reason === 'compromised' ? 'Your password appeared in a data breach of another website. Choose a new one to continue.' : 'You need to choose a new password before continuing.'"
    :$size
>
    {{ $slot }}

    @if ($currentPassword)
        <tk:password
            :attributes="$attributes->prefixed('current-password:')"
            placeholder
            label="Current password"
            :$size
            name="current_password"
            required
            autocomplete="current-password"
        />
    @endif

    <tk:password
        :attributes="$attributes->prefixed('new-password:')"
        placeholder
        label="New password"
        :$size
        name="password"
        required
        autocomplete="new-password"
    />

    <tk:password
        :attributes="$attributes->prefixed('new-password-confirmation:')"
        placeholder
        label="New password confirmation"
        :$size
        name="password_confirmation"
        required
        autocomplete="new-password"
    />

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')"
        label="Change password"
        :$size
        variant="accent"
    />

    @if ($logoutUrl)
        <x-slot:append>
            <tk:link
                :attributes="$attributes->prefixed('logout:')"
                label="Log out"
                :href="$logoutUrl"
                :$size
            />
        </x-slot:append>
    @endif
</tk:form.section>
