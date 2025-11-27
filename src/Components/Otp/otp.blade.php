<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :$label
>
    <div
        {{ $attributes->classes('flex items-center gap-2 isolate w-fit [&_[data-tallkit-input-group]]:w-auto') }}
        {{ $buildDataAttribute('control') }}
        role="group"
    >
        @if ($slot->isEmpty() && $length)
            @for ($i = 0; $i < $length; $i++)
                <tk:otp.input type="{{ $private ? 'password' : 'text' }}" />
            @endfor
        @else
            {{ $slot }}
        @endif
    </div>
</tk:field.wrapper>
