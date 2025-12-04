<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :$label
>
    <div
        x-data="otp"
        x-modelable="value"
        wire:ignore
        role="group"
        {{ $buildDataAttribute('control') }}
        {{
            $attributes
            ->whereDoesntStartWith(['input:'])
            ->classes('
                flex items-center gap-2
                isolate w-fit
                [&_[data-tallkit-input-group]]:w-auto
            ')
        }}
    >
        @if ($slot->isEmpty() && $length)
            @for ($i = 0; $i < $length; $i++)
                <tk:otp.input :attributes="$attributesAfter('input:')" />
            @endfor
        @else
            {{ $slot }}
        @endif
    </div>
</tk:field.wrapper>
