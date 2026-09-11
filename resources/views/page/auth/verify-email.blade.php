@props([
    'logoutUrl' => null,
    'size' => null,
])
@php

$logoutUrl ??= route_detect(['logout', 'auth.logout'], default: null);

@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['submit:', 'logout:'])"
    :$size
    title="Verify Your Email Address"
    subtitle="Before proceeding, please check your email for a verification link."
>
    {{ $slot }}

    <tk:submit
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'submit:')->classes('w-full')"
        :$size
        label="Resend verification email"
        variant="accent"
    />

    @if ($logoutUrl)
        <x-slot:append>
            <tk:link
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'logout:')"
                :href="$logoutUrl"
                :$size
                label="Log out"
            />
        </x-slot:append>
    @endif
</tk:form.section>
