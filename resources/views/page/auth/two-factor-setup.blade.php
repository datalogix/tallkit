@props([
    'enabled' => false,
    'method' => 'totp',
    'secret' => null,
    'uri' => null,
    'qrSvg' => null,
    'secretUnreadable' => false,
    'canDisable' => true,
    'awaitingContinue' => false,
    'recoveryCodes' => [],
    'recoveryCodesCount' => 0,
    'canManageRecoveryCodes' => false,
    'trustedDevices' => [],
    'prepare' => 'prepare',
    'enable' => 'enable',
    'disable' => 'disable',
    'continue' => 'continueAfterSetup',
    'regenerate' => 'regenerateRecoveryCodes',
    'revokeDevice' => 'revokeTrustedDevice',
    'revokeAllDevices' => 'revokeAllTrustedDevices',
])
<tk:section
    :attributes="$attributes->whereDoesntStartWith(['qr:', 'recovery-codes:', 'devices:'])"
    title="Two-factor setup"
    :subtitle="$enabled ? 'Two-factor authentication is enabled for your account.' : 'Two-factor authentication is disabled.'"
    :separator="false"
>
    @if ($enabled)
        @if ($secretUnreadable)
            <tk:text label="We could not verify your two-factor secret. Please disable and set up two-factor authentication again." />
        @endif

        @if ($canDisable && ! $awaitingContinue)
            <tk:button :action="$disable" label="Disable two-factor" :wire:confirm="__('Disable two-factor authentication?')" />
        @endif

        @if (count($recoveryCodes) > 0)
            <tk:separator />
            <tk:two-factor.recovery-codes :attributes="$attributes->prefixed('recovery-codes:')" :codes="$recoveryCodes" />
        @elseif ($canManageRecoveryCodes)
            <tk:separator />
            <tk:text :label="__('Recovery codes are configured for your account (:count available).', ['count' => $recoveryCodesCount])" />
        @endif

        @if ($awaitingContinue)
            <tk:button :action="$continue" label="Continue" />
        @elseif (count($recoveryCodes) > 0 || $canManageRecoveryCodes)
            <tk:button :action="$regenerate" label="Regenerate recovery codes" />
        @endif

        @if (count($trustedDevices) > 0)
            <tk:separator />
            <tk:text label="Trusted devices:" />
            <tk:page.auth.devices
                :attributes="$attributes->prefixed('devices:')"
                revoke:label="Revoke"
                revoke-all:label="Revoke all trusted devices"
                :revoke:wire:confirm="__('Revoke this device?')"
                :revoke-all:wire:confirm="__('Revoke all trusted devices?')"
                :items="$trustedDevices"
                :revoke="$revokeDevice"
                :revoke-all="$revokeAllDevices"
            />
        @endif
    @else
        <tk:button :action="$prepare" label="Generate setup secret" />

        @if ($secret)
            <div class="space-y-6">
                <tk:separator />

                @if ($method === 'totp')
                    <tk:two-factor.qr :attributes="$attributes->prefixed('qr:')" :svg="$qrSvg" :$secret :$uri />
                @else
                    <tk:text
                        :label="match ($method) {
                            'email' => 'We sent a 6-digit verification code to your email. Enter it below to enable two-factor authentication.',
                            'sms' => 'We sent a 6-digit verification code by SMS. Enter it below to enable two-factor authentication.',
                            default => false,
                        }"
                    />
                @endif

                <tk:form :action="$enable" submit:label="Enable two-factor">
                    <tk:otp label="Verification code" name="code" />
                </tk:form>
            </div>
        @endif
    @endif
</tk:section>
