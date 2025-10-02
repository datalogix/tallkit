<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['submit:', 'logout:'])"
    title="Verify Your Email Address"
    subtitle="Before proceeding, please check your email for a verification link."
>
    <tk:submit
        :attributes="$attributesAfter('submit:')->classes('w-full')"
        label="Resend verification email"
        variant="accent"
    />

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
