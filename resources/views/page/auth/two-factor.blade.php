@props([
    'size' => null,
    'method' => null,
    'resendUrl' => null,
])
@php

$resendUrl ??= route_detect(['two-factor.resend', 'auth.two-factor.resend'], default: null);

@endphp
<tk:form.section
    :attributes="$attributes->whereDoesntStartWith(['code:', 'recovery-code:', 'recovery-toggle:', 'submit:', 'resend:'])->merge(['title' => 'Two-factor authentication'])"
    :$size
    :subtitle="match ($method) {
        'totp' => 'Enter the 6-digit authentication code from your authenticator app or a recovery code.',
        'email' => 'Enter the 6-digit authentication code sent to your email or a recovery code.',
        'sms' => 'Enter the 6-digit authentication code sent by SMS or a recovery code.',
        default => 'Enter your authentication code or a recovery code.',
    }"
>
    @if (in_array($method, ['totp', 'email', 'sms'], true))
        <div x-data="{ recovery: false }" class="space-y-6">
            <template x-if="! recovery">
                <tk:otp
                    :attributes="$attributes->prefixed('code:')->merge(['label' => 'Authentication code'])"
                    :$size
                    name="code"
                />
            </template>

            <template x-if="recovery">
                <tk:input
                    :attributes="$attributes->prefixed('recovery-code:')->merge(['label' => 'Recovery code'])"
                    :$size
                    name="recovery_code"
                    maxlength="64"
                    autocomplete="off"
                    x-init="$el.focus()"
                />
            </template>

            <tk:button
                :attributes="$attributes->prefixed('recovery-toggle:')"
                :$size
                variant="ghost"
                x-on:click="recovery = ! recovery"
            >
                <span x-show="! recovery">{{ __('Use a recovery code') }}</span>
                <span x-show="recovery" x-cloak>{{ __('Use an authentication code') }}</span>
            </tk:button>
        </div>
    @else
        <tk:input
            :attributes="$attributes->prefixed('code:')->merge(['label' => 'Authentication or recovery code'])"
            :$size
            name="code"
            maxlength="64"
            autocomplete="one-time-code"
        />
    @endif

    {{ $slot }}

    <tk:submit
        :attributes="$attributes->prefixed('submit:')->classes('w-full')->merge(['label' => 'Verify code'])"
        :$size
        variant="accent"
    />

    @if (in_array($method, ['email', 'sms'], true))
        @if (in_livewire())
            <tk:button
                :attributes="$attributes->prefixed('resend:')->merge(['label' => 'Resend code'])"
                :$size
                action="resend"
            />
        @else
            <tk:button
                :attributes="$attributes->prefixed('resend:')->merge(array_filter([
                    'formaction' => $resendUrl,
                    'formnovalidate' => true,
                    'name' => $resendUrl ? null : 'resend',
                    'value' => $resendUrl ? null : '1',
                ]))
                    ->merge(['label' => 'Resend code'])"
                :$size
                type="submit"
            />
        @endif
    @endif
</tk:form.section>
