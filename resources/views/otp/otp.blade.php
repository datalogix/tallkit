@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'length' => null,
    'private' => null,
    'mode' => null,
    'submit' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel] = TALLKit::resolveFieldContext($attributes, $label);

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <div
        wire:ignore
        x-data="otp(@js($submit))"
        x-modelable="value"
        role="group"
    >
        <tk:field.control
            :$size
            :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
                ->classes(
                    '
                        w-fit
                        flex items-center
                        isolate
                    ',
                    match ($size) {
                        'xs' => 'gap-1',
                        'sm' => 'gap-1.5',
                        default => 'gap-2',
                        'lg' => 'gap-2.5',
                        'xl' => 'gap-3',
                        '2xl' => 'gap-3.5',
                        '3xl' => 'gap-4',
                    }
                )
            "
        >
            @if ($slot->isEmpty() && ($length ?? 6))
                @for ($i = 0; $i < ($length ?? 6); $i++)
                    <tk:otp.input :$attributes />
                @endfor
            @else
                {{ $slot }}
            @endif
        </tk:field.control>
    </div>
</tk:field.wrapper>
