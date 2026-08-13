@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'format' => null,
    'private' => null,
    'mode' => null,
    'submit' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext($attributes, $label, $id);
$format ??= '999999';
$groups = explode('-', $format);
$describedBy = TALLKit::ariaDescribedBy($id, $description, $help, $invalid, $showError);

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
        id="{{ $id }}"
        @if ($label) aria-label="{{ __($label) }}" @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        {{ $attributes->whereStartsWith('wire:')->merge(['wire:model' => $wireModel]) }}
    >
        @if ($name && !$wireModel)
            <input type="hidden" name="{{ $name }}" x-model="value" />
        @endif

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
