@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'format' => null,
    'private' => null,
    'mode' => null,
    'submit' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel] = TALLKit::resolveFieldContext($attributes, $label);
$format ??= '999999';
$groups = explode('-', $format);

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <div
        x-data="otp(@js($submit))"
        x-modelable="value"
        role="group"
        {{ $attributes->whereStartsWith('wire:')->merge(['wire:model' => $wireModel]) }}
    >
        <tk:field.control
            :$size
            :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
                ->classes(
                    'w-fit flex items-center isolate',
                    TALLKit::gap(size: $size),
                )
            "
        >
            @if ($slot->isNotEmpty())
                {{ $slot }}
            @elseif (count($groups) > 1)
                @foreach ($groups as $group)
                    <tk:otp.group>
                        @for ($i = 0; $i < strlen($group); $i++)
                            <tk:otp.input
                                :attributes="$attributes->whereDoesntStartWith('wire:')"
                                :$invalid
                                :mode="match (strtoupper($group[$i])) {
                                    'A' => 'alpha',
                                    '9' => 'numeric',
                                    default => 'alphanumeric',
                                }"
                            />
                        @endfor
                    </tk:otp.group>

                    @if (! $loop->last)
                        <tk:otp.separator />
                    @endif
                @endforeach
            @else
                @for ($i = 0; $i < strlen($format); $i++)
                    <tk:otp.input
                        :attributes="$attributes->whereDoesntStartWith('wire:')"
                        :$invalid
                        :mode="match (strtoupper($format[$i])) {
                            'A' => 'alpha',
                            '9' => 'numeric',
                            default => 'alphanumeric',
                        }"
                    />
                @endfor
            @endif
        </tk:field.control>
    </div>
</tk:field.wrapper>
