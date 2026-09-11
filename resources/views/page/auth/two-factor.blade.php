@props([
    'size' => null,
    'method' => null,
])
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['code:', 'submit:', 'resend:'])"
    :$size
    title="Two-factor authentication"
    :subtitle="match ($method) {
        'totp' => 'Enter the 6-digit authentication code from your authenticator app or a recovery code.',
        'email' => 'Enter the 6-digit authentication code sent to your email or a recovery code.',
        'sms' => 'Enter the 6-digit authentication code sent by SMS or a recovery code.',
        default => 'Enter your authentication code or a recovery code.',
    }"
>
    @if (in_array($method, ['totp', 'email', 'sms'], true))
        <tk:otp
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'code:')"
            :$size
            label="Authentication or recovery code"
            name="code"
        />
    @else
        <tk:input
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'code:')"
            :$size
            label="Authentication or recovery code"
            name="code"
            maxlength="64"
            autocomplete="one-time-code"
        />
    @endif

    {{ $slot }}

    <tk:submit
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'submit:')->classes('w-full')"
        :$size
        label="Verify code"
        variant="accent"
    />

    @if (in_array($method, ['email', 'sms'], true))
        <tk:button
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'resend:')"
            :$size
            label="Resend code"
            action="resend"
        />
    @endif
</tk:form.section>
