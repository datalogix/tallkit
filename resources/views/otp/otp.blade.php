@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'format' => null,
    'private' => null,
    'charset' => null,
    'submit' => null,
])
@php

$boxPlaceholder = string_value($placeholder);
[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = $attributes->fieldContext(label: $label, id: $id, scope: get_defined_vars());

$format ??= str_repeat(match ($charset) { 'alpha' => 'A', 'alphanumeric' => '*', default => '9' }, 6);
$groups = explode('-', $format);
$digitCount = strlen(str_replace('-', '', $format));
$digitIndex = 0;

@endphp
<tk:field.wrapper
    :$name
    :attributes="$attributes->withFieldProps(get_defined_vars())"
>
    <div
        wire:ignore
        x-data="otp({{ Js::from($submit) }})"
        role="group"
        dir="ltr"
        id="{{ $id }}"
        aria-label="{{ $label ? __($label) : __('One-time passcode') }}"
        {{
            $attributes->whereStartsWith('wire:')
                ->whereDoesntStartWith('wire:model')
                ->merge([
                    'aria-describedby' => TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError)
                ])
        }}
    >
        <input
            type="hidden"
            {{
                $attributes->prefixed('hidden:')
                    ->dataKey('otp-field')
                    ->merge([
                        'name' => $name,
                        'value' => unless_livewire($value),
                    ])
                    ->merge($attributes->whereStartsWith(['wire:model', 'x-model'])->getAttributes() ?: array_filter(['wire:model' => $wireModel]), false)
            }}
        />

        <tk:field.control
            :$size
            :attributes="$attributes->withFieldControlProps(get_defined_vars())
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
                            @php($digitIndex++)
                            <tk:otp.input
                                :attributes="$attributes->whereDoesntStartWith(['wire:', 'x-model', 'hidden:'])"
                                :$invalid
                                :placeholder="$boxPlaceholder"
                                :aria-label="__('Digit :n of :total', ['n' => $digitIndex, 'total' => $digitCount])"
                                :first="$digitIndex === 1"
                                :total="$digitCount"
                                :charset="match (strtoupper($group[$i])) {
                                    'A' => 'alpha',
                                    '9' => 'numeric',
                                    default => 'alphanumeric',
                                }"
                            />
                        @endfor
                    </tk:otp.group>

                    @unless ($loop->last)
                        <tk:otp.separator />
                    @endunless
                @endforeach
            @else
                @for ($i = 0; $i < strlen($format); $i++)
                    @php($digitIndex++)
                    <tk:otp.input
                        :attributes="$attributes->whereDoesntStartWith(['wire:', 'x-model', 'hidden:'])"
                        :$invalid
                        :placeholder="$boxPlaceholder"
                        :aria-label="__('Digit :n of :total', ['n' => $digitIndex, 'total' => $digitCount])"
                        :first="$digitIndex === 1"
                        :total="$digitCount"
                        :charset="match (strtoupper($format[$i])) {
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
