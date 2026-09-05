@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'format' => null,
    'private' => null,
    'mode' => null,
    'submit' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext(attributes: $attributes, label: $label, id: $id);

$format ??= '999999';
$groups = explode('-', $format);
$digitCount = strlen(str_replace('-', '', $format));
$digitIndex = 0;

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <div
        wire:ignore
        x-data="otp(@js($submit))"
        role="group"
        id="{{ $id }}"
        aria-label="{{ $label ? __($label) : __('One-time passcode') }}"
        {{
            $attributes->whereStartsWith('wire:')
                ->except('wire:model')
                ->merge([
                    'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError)
                ])
        }}
    >
        <input
            type="hidden"
            {{
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'hidden:')
                    ->dataKey('otp-field')
                    ->merge([
                        'name' => $name,
                        'value' => in_livewire() ? null : $value,
                        'wire:model' => $wireModel,
                    ])
            }}
        />

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
                                :attributes="$attributes->whereDoesntStartWith(['wire:', 'hidden:'])"
                                :$invalid
                                :aria-label="__('Digit :n of :total', ['n' => ++$digitIndex, 'total' => $digitCount])"
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
                        :attributes="$attributes->whereDoesntStartWith(['wire:', 'hidden:'])"
                        :$invalid
                        :aria-label="__('Digit :n of :total', ['n' => ++$digitIndex, 'total' => $digitCount])"
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
