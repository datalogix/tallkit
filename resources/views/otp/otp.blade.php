@props([
    'size' => null,
    'length' => null,
    'private' => null,
    'mode' => null,
    'submit' => null,
])
<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :$label
>
    <tk:field.control
        :$attributes
        :$size
        control:class="w-fit"
    >
        <div
            wire:ignore
            x-data="otp(@js($submit))"
            x-modelable="value"
            role="group"
            data-tallkit-control
            {{
                $attributes
                    ->whereDoesntStartWith(['input:'])
                    ->classes(
                        '
                            flex items-center
                            isolate w-fit
                            [&_[data-tallkit-input-group]]:w-auto
                        ',
                        TALLKit::gap(size: $size)
                    )
            }}
        >
            @if ($slot->isEmpty() && ($length ?? 6))
                @for ($i = 0; $i < ($length ?? 6); $i++)
                    <tk:otp.input :attributes="TALLKit::attributesAfter($attributes, 'input:')" />
                @endfor
            @else
                {{ $slot }}
            @endif
        </div>
    </tk:field.control>
</tk:field.wrapper>
