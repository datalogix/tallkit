@props([
    'logout' => null,
    'size' => null,
])
@php

$logout ??= route_detect(['logout', 'auth.logout'], default: null);

@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['submit:', 'logout:'])"
    :$size
    title="Verify Your Email Address"
    subtitle="Before proceeding, please check your email for a verification link."
>
    <tk:submit
        :attributes="TALLKit::attributesAfter($attributes, 'submit:')->classes('w-full')"
        :$size
        label="Resend verification email"
        variant="accent"
    />

    @if ($logout)
        <x-slot:append>
            <tk:link
                :attributes="TALLKit::attributesAfter($attributes, 'logout:')"
                :href="$logout"
                :$size
                label="Log out"
            />
        </x-slot:append>
    @endif
</tk:form.section>
