
<tk:section
    :attributes="$attributes->whereDoesntStartWith(['text:', 'resend:', 'logout:'])"
    title="Verify Your Email Address"
>
    <tk:text
        :attributes="$attributesAfter('text:')"
        label="Before proceeding, please check your email for a verification link."
    />

    <tk:button
        :attributes="$attributesAfter('resend:')"
        icon="email-arrow-right-outline"
        label="Resend verification email"
        action="resend"
    />

    @if ($logout)
        <tk:element
            :attributes="$attributesAfter('logout:')->classes('text-sm')"
            :href="$logout"
            label="Log out"
        />
    @endif
</tk:section>
