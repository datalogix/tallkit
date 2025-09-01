
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['logout:'])"
    title="Verify Your Email Address"
    subtitle="Before proceeding, please check your email for a verification link."
    submit:label="Resend verification email"
    submit:variant="accent"
>
    @if ($logout)
        <x-slot:append>
            <tk:link
                :attributes="$attributesAfter('logout:')"
                :href="$logout"
                label="Log out"
            />
        </x-slot:append>
    @endif
</tk:form.section>
