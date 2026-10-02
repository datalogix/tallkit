@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'rows' => null,
    'maxRows' => 10,
    'submit' => null,
    'inline' => null,
    'header' => null,
    'footer' => null,
    'actionsLeading' => null,
    'actionsTrailing' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::fieldContext(attributes: $attributes, label: $label, id: $id, scope: get_defined_vars());
$value = TALLKit::fieldOldValue($fieldName, $value);

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <div
        wire:ignore
        x-data="composer({ submit: @js($submit) })"
        x-modelable="value"
        role="group"
        {{
            $attributes
                ->dataKey('control')
                ->dataKey('group-target')
                ->merge([
                    'aria-labelledby' => $label ? $id.'-label' : null,
                    'data-invalid' => $invalid ? true : null,
                    'data-inline' => $inline ? true : null,
                ])
                ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: [
                    'hidden:', 'header:', 'input:', 'textarea:', 'footer:', 'actions-leading:', 'actions-trailing:',
                ]))
                ->classes(
                    '
                        grid
                        grid-cols-[auto_1fr_1fr_auto]

                        peer
                        w-full
                        appearance-none
                        [print-color-adjust:exact]

                        tk-control-surface
                        tk-control-focus-ring-nested
                        tk-control-disabled
                        tk-control-invalid-border-self-nested-focus

                        [&[disabled]]:pointer-events-none
                    ',
                    TALLKit::fontSize(size: $size),
                    TALLKit::roundedSize(size: $size, mode: 'large'),
                    TALLKit::padding(size: $size),
                    TALLKit::controlFocusRingNested(color: $color),
                )
        }}
    >
        <input
            type="hidden"
            {{
                $attributes->prefixed('hidden:')
                    ->dataKey('composer')
                    ->merge([
                        'name' => $name,
                        'value' => in_livewire() ? null : $value,
                        'wire:model' => $wireModel,
                    ])
            }}
        />

        @if ($header && ! $inline)
            <div {{ $attributes->prefixed('header:')->classes(
                'flex items-center col-span-3',
                TALLKit::marginBottom(size: $size),
                TALLKit::gap(size: $size, mode: 'smallest'),
            ) }}>
                {{ $header }}
            </div>
        @endif

        <tk:field.control
            :$size
            :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
                ->classes(
                    '
                        col-span-4
                        [[data-inline]_&]:col-span-2
                        [[data-inline]_&]:col-start-2

                        [&_[data-tallkit-control]]:p-0!
                        [&_[data-tallkit-control]]:h-auto!
                        [&_[data-tallkit-control]]:bg-transparent!
                        [&_[data-tallkit-control]]:border-none!
                        [&_[data-tallkit-control]]:outline-none!
                        [&_[data-tallkit-control]]:ring-0!
                        [&_[data-tallkit-control]]:resize-none!
                        [&_[data-tallkit-control]]:shadow-none!
                        [&_[data-tallkit-control]]:rounded-none!

                        [&_[data-tallkit-field-control-prepend]]:p-0!
                        [&_[data-tallkit-field-control-prepend]]:pe-3!
                        [&_[data-tallkit-field-control-append]]:p-0!
                        [&_[data-tallkit-field-control-append]]:ps-3!
                    '
                )"
        >
            @isset ($input)
                {{ $input }}
            @else
                <tk:textarea
                    :attributes="$attributes->prefixed('textarea:')
                        ->merge(array_filter([
                            'disabled' => TALLKit::isAttributeEnabled($attributes->get('disabled')),
                            'readonly' => TALLKit::isAttributeEnabled($attributes->get('readonly')),
                            'aria-describedby' => TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                            'aria-invalid' => $invalid ? 'true' : null,
                        ]))"
                    area:class="flex-1"
                    :$id
                    :$size
                    :$placeholder
                    :$maxRows
                    :$value
                    :label="false"
                    :rows="$rows ?? ($inline ? 1 : 2)"
                >{{ $slot }}</tk:textarea>
            @endisset
        </tk:field.control>

        @if ($footer && ! $inline)
            <div {{ $attributes->prefixed('footer:')->classes(
                'flex items-center col-span-3',
                TALLKit::marginTop(size: $size),
                TALLKit::gap(size: $size, mode: 'smallest'),
            ) }}>
                {{ $footer }}
            </div>
        @endif

        @isset ($actionsLeading)
            <div {{ $attributes->prefixed('actions-leading:')->classes(
                '
                    flex items-start col-span-2
                    [[data-inline]_&]:col-span-1
                    [[data-inline]_&]:col-start-1
                    [[data-inline]_&]:row-start-1
                ',
                TALLKit::gap(size: $size, mode: 'smallest'),
            ) }}>
                {{ $actionsLeading ?? '' }}
            </div>
        @endisset

        @isset ($actionsTrailing)
            <div {{ $attributes->prefixed('actions-trailing:')->classes(
                '
                    flex items-start justify-end col-span-2
                    [[data-inline]_&]:col-span-1
                ',
                TALLKit::gap(size: $size, mode: 'smallest'),
            ) }}>
                {{ $actionsTrailing ?? '' }}
            </div>
        @endisset
    </div>
</tk:field.wrapper>
