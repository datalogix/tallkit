@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'ticks' => null,
    'showValue' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::fieldContext(attributes: $attributes, label: $label, id: $id, scope: get_defined_vars());
$value = TALLKit::fieldOldValue($fieldName, $value);
$hasControl = $prepend || $icon || $append || $loading || $iconTrailing || $kbd || $attributes->has('class');

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
    :prefix="false"
    :suffix="false"
>
    @if ($showValue)
        <x-slot:labelAppend>
            {{ $labelAppend ?? '' }}

            <span
                {{ TALLKit::dataKey('slider-value') }}="{{ $id }}"
                wire:ignore
                aria-hidden="true"
                {{ $attributes->prefixed('value:')->classes(TALLKit::fontSize(size: TALLKit::adjustSize(size: $size)), TALLKit::textNeutral(variant: 'subtle'), 'tabular-nums') }}
            >{{ $value }}</span>
        </x-slot:labelAppend>
    @endif

    <tk:field.control
        :$size
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
            ->when(
                $hasControl,
                fn ($attrs) => $attrs->classes(
                    '
                        flex
                        items-center
                        gap-x-3
                        [&_[data-tallkit-field-control-prepend]]:ps-0
                        [&_[data-tallkit-field-control-append]]:pe-0
                    '
                )
            )
        "
    >
        <div
            wire:ignore
            x-data="slider"
            {{ $attributes->prefixed('slider:')->classes('w-full block space-y-1.5') }}
        >
            <input
                type="range"
                {{
                    $attributes
                        ->dataKey('slider')
                        ->dataKey('control')
                        ->dataKey('group-target')
                        ->merge([
                            'name' => $name,
                            'id' => $id,
                            'value' => in_livewire() ? null : $value,
                            'wire:model' => $wireModel,
                            'aria-describedby' => TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                            'aria-invalid' => $invalid ? 'true' : null,
                            'data-invalid' => $invalid ? true : null,
                        ])
                        ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: ['slider:', 'ticks:', 'tick:', 'value:']))
                        ->classes(
                            '
                                [--range-active:rgb(0_0_0_/_.8)]
                                dark:[--range-active:rgb(255_255_255)]

                                [--range-base:rgb(0_0_0_/_.1)]
                                dark:[--range-base:rgb(255_255_255_/_.1)]

                                relative
                                flex-1
                                peer
                                block
                                w-full
                                appearance-none
                                [print-color-adjust:exact]

                                bg-[var(--range-base)]

                                text-zinc-700
                                dark:text-zinc-300

                                border
                                border-zinc-300
                                dark:border-white/10
                                shadow-xs

                                tk-control-invalid-border
                                tk-control-disabled

                                outline-none

                                focus-visible:[&::-webkit-slider-thumb]:outline-2
                                focus-visible:[&::-webkit-slider-thumb]:outline-offset-0
                                focus-visible:[&::-webkit-slider-thumb]:ring-2

                                focus-visible:[&::-moz-range-thumb]:outline-2
                                focus-visible:[&::-moz-range-thumb]:outline-offset-0
                                focus-visible:[&::-moz-range-thumb]:ring-2

                                before:absolute
                                before:inset-y-0
                                before:start-0
                                before:w-[var(--range-percent)]
                                before:bg-[var(--range-active)]
                                before:pointer-events-none
                                before:content-[\'\']
                                before:z-0

                                disabled:before:opacity-0
                                data-low:before:rounded-e-none!

                                [&::-webkit-slider-thumb]:relative
                                [&::-webkit-slider-thumb]:z-10
                                [&::-webkit-slider-thumb]:appearance-none
                                [&::-webkit-slider-thumb]:border
                                [&::-webkit-slider-thumb]:border-zinc-300
                                dark:[&::-webkit-slider-thumb]:border-white/10
                                [&::-webkit-slider-thumb]:size-5
                                [&::-webkit-slider-thumb]:bg-white
                                [&::-webkit-slider-thumb]:rounded-full
                                [&::-webkit-slider-thumb]:shadow
                                [&::-webkit-slider-thumb]:transition-colors
                                [&::-webkit-slider-thumb]:duration-150

                                [&::-moz-range-thumb]:relative
                                [&::-moz-range-thumb]:z-10
                                [&::-moz-range-thumb]:appearance-none
                                [&::-moz-range-thumb]:border
                                [&::-moz-range-thumb]:border-zinc-300
                                dark:[&::-moz-range-thumb]:border-white/10
                                [&::-moz-range-thumb]:size-5
                                [&::-moz-range-thumb]:bg-white
                                [&::-moz-range-thumb]:rounded-full
                                [&::-moz-range-thumb]:shadow
                                [&::-moz-range-thumb]:transition-colors
                                [&::-moz-range-thumb]:duration-150
                            ',
                            TALLKit::height(size: $size, mode: 'smallest'),
                            TALLKit::fontSize(size: $size, mode: 'large'),
                            TALLKit::roundedSize(size: $size, mode: 'large', after: null, before: true),
                            match ($size) {
                                'xs' => '[&::-webkit-slider-thumb]:size-3.5 [&::-moz-range-thumb]:size-3.5',
                                'sm' => '[&::-webkit-slider-thumb]:size-4 [&::-moz-range-thumb]:size-4',
                                default => '[&::-webkit-slider-thumb]:size-4.5 [&::-moz-range-thumb]:size-4.5',
                                'lg' => '[&::-webkit-slider-thumb]:size-5 [&::-moz-range-thumb]:size-5',
                                'xl' => '[&::-webkit-slider-thumb]:size-5.5 [&::-moz-range-thumb]:size-5.5',
                                '2xl' => '[&::-webkit-slider-thumb]:size-6 [&::-moz-range-thumb]:size-6',
                                '3xl' => '[&::-webkit-slider-thumb]:size-6.5 [&::-moz-range-thumb]:size-6.5',
                            },
                            match ($color) {
                                'accent' => '
                                    focus-visible:[&::-webkit-slider-thumb]:outline-[var(--color-accent)]
                                    focus-visible:[&::-webkit-slider-thumb]:ring-[var(--color-accent)]/20
                                    focus-visible:[&::-moz-range-thumb]:outline-[var(--color-accent)]
                                    focus-visible:[&::-moz-range-thumb]:ring-[var(--color-accent)]/20
                                ',
                                default => TALLKit::sliderFocusRing(color: $color) ?? '
                                    focus-visible:[&::-webkit-slider-thumb]:outline-blue-700 dark:focus-visible:[&::-webkit-slider-thumb]:outline-blue-300
                                    focus-visible:[&::-webkit-slider-thumb]:ring-blue-700/20 dark:focus-visible:[&::-webkit-slider-thumb]:ring-blue-300/20
                                    focus-visible:[&::-moz-range-thumb]:outline-blue-700 dark:focus-visible:[&::-moz-range-thumb]:outline-blue-300
                                    focus-visible:[&::-moz-range-thumb]:ring-blue-700/20 dark:focus-visible:[&::-moz-range-thumb]:ring-blue-300/20
                                ',
                            },
                        )
                }}
            />

            @if ($slot->hasActualContent() || $ticks)
                <div
                    {{
                        $attributes->prefixed('ticks:')
                            ->dataKey('slider-ticks')
                            ->classes('flex justify-between')
                    }}
                >
                    @foreach (collect($ticks) as $tick)
                        <tk:slider.tick
                            :attributes="$attributes->prefixed('tick:')
                                ->merge(TALLKit::attributesFromItem($tick), false)
                            "
                            :$size
                        />
                    @endforeach

                    {{ $slot }}
                </div>
            @endif
        </div>
    </tk:field.control>
</tk:field.wrapper>
