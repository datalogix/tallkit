@props([
    'logoutUrl' => null,
    'size' => null,
])
@php

$logoutUrl ??= route_detect(['logout', 'auth.logout'], default: null);

@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['submit:', 'logout:'])"
    title="Verify your email address"
    subtitle="Before proceeding, please check your email for a verification link."
    :$size
>
    {{ $slot }}

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')"
        label="Resend verification email"
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
