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

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext(attributes: $attributes, label: $label, id: $id);

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <div
        wire:ignore
        x-data="composer({ submit: @js($submit), placeholder: @js($placeholder ? __($placeholder) : null) })"
        x-modelable="value"
        role="group"
        {{
            $attributes
                ->merge([
                    'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                    'aria-invalid' => $invalid ? 'true' : null,
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

                        [&[disabled]]:border-zinc-200
                        dark:[&[disabled]]:border-white/5

                        [&[data-invalid]:not(:has([data-tallkit-control]:focus-visible))]:border-red-500
                        dark:[&[data-invalid]:not(:has([data-tallkit-control]:focus-visible))]:border-red-400

                        disabled:[&[data-invalid]:not(:has([data-tallkit-control]:focus-visible))]:border-red-500
                        dark:disabled:[&[data-invalid]:not(:has([data-tallkit-control]:focus-visible))]:border-red-400

                        [&[disabled]]:shadow-none
                        [&[disabled]]:[&[data-invalid]]:shadow-none

                        [&[disabled]]:opacity-50
                        dark:[&[disabled]]:opacity-40

                        [&[disabled]]:cursor-not-allowed
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
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'hidden:')
                    ->dataKey('composer')
                    ->merge([
                        'name' => $name,
                        'value' => in_livewire() ? null : $value,
                        'wire:model' => $wireModel,
                    ])
            }}
        />

        @if ($header && !$inline)
            <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'header:')->classes(
                'flex items-center col-span-3',
                TALLKit::marginBottom(size: $size),
                TALLKit::gap(size: $size, mode: 'smallest'),
            ) }}>
                {{ $header }}
            </div>
        @endif

        <tk:field.control
            :$size
            :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
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
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'textarea:')"
                    counter:class="flex-1"
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

        @if ($footer && !$inline)
            <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'footer:')->classes(
                'flex items-center col-span-3',
                TALLKit::marginTop(size: $size),
                TALLKit::gap(size: $size, mode: 'smallest'),
            ) }}>
                {{ $footer }}
            </div>
        @endif

        @isset ($actionsLeading)
            <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'actions-leading:')->classes(
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
            <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'actions-trailing:')->classes(
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
