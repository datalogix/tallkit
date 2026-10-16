@props([
    'svg' => null,
    'secret' => null,
    'uri' => null,
])
<div {{ $attributes->whereDoesntStartWith(['qr:', 'secret:', 'uri:'])->classes('space-y-6') }}>
    @if ($svg)
        <div
            {{ $attributes->prefixed('qr:')->classes('mx-auto w-fit') }}
            role="img"
            aria-label="{{ __('QR code for authenticator app setup') }}"
        >{!! $svg !!}</div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">
        @foreach (['secret' => [$secret, 'Use this secret in your authenticator app:', 'Copy secret'], 'uri' => [$uri, 'OTPAuth URI (for QR generation):', 'Copy URI']] as $part => [$value, $label, $copy])
            @if ($value)
                <div class="space-y-2">
                    <tk:text :attributes="$attributes->prefixed($part.':label:')" :label="$label" />
                    <pre {{ $attributes->prefixed($part.':value:')->classes('whitespace-pre-wrap break-all border border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-white/5 p-4 rounded-lg') }}>{{ $value }}</pre>
                    <tk:copyable :attributes="$attributes->prefixed($part.':copy:')" :label="$copy" :content="$value" />
                </div>
            @endif
        @endforeach
    </div>
</div>
