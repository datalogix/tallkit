@props([
    ...TALLKit::fieldProps(),
    'ticks' => null,
    'displayValue' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel] = TALLKit::resolveFieldContext($attributes, $label);

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
    :prefix="false"
    :suffix="false"
>
    @if (! isset($labelAppend) && $displayValue !== false && in_livewire() && isset(${$name}))
        <x-slot:labelAppend>
            <span wire:text="{{ $name }}"></span>
        </x-slot:labelAppend>
    @endif

    <div
        wire:ignore
        x-data="slider"
        {{ TALLKit::attributesAfter($attributes, 'slider:')->classes('w-full block space-y-1.5') }}
    >
        <input
            type="range"
            @if ($name) name="{{ $name }}" @endif
            @if ($id) id="{{ $id }}" @endif
            @if ($invalid) aria-invalid="true" data-invalid @endif
            @unless (in_livewire()) value="{{ $value }}" @endif
            {{
                $attributes
                    ->dataKey('slider')
                    ->dataKey('control')
                    ->dataKey('group-target')
                    ->merge(['wire:model' => $wireModel])
                    ->whereDoesntStartWith([
                        'field:', 'label:', 'info:', 'badge:', 'description:',
                        'group:', 'prefix:', 'suffix:',
                        'help:', 'error:',
                        'control:', 'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:',
                        'slider:', 'ticks:', 'tick:',
                    ])
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
                            disabled:text-zinc-500
                            dark:text-zinc-300
                            dark:disabled:text-zinc-400

                            disabled:cursor-not-allowed
                            disabled:resize-none

                            border
                            border-zinc-200
                            dark:border-white/10

                            disabled:border-zinc-100
                            dark:disabled:border-white/5

                            [&[data-invalid]:not(:focus-visible)]:border-red-500
                            dark:[&[data-invalid]:not(:focus-visible)]:border-red-400

                            disabled:[&[data-invalid]:not(:focus-visible)]:border-red-500
                            dark:disabled:[&[data-invalid]:not(:focus-visible)]:border-red-400

                            shadow-xs
                            disabled:shadow-none
                            [&[data-invalid]]:disabled:shadow-none

                            shadow-xs
                            disabled:shadow-none
                            [&[data-invalid]]:disabled:shadow-none

                            disabled:opacity-75
                            dark:disabled:opacity-50

                            focus-visible:outline-2
                            focus-visible:outline-blue-700
                            dark:focus-visible:outline-blue-300
                            focus-visible:outline-offset-0

                            focus-visible:ring-2
                            focus-visible:ring-blue-700/20
                            dark:focus-visible:ring-blue-300/20

                            before:absolute
                            before:inset-y-0
                            before:left-0
                            before:w-[var(--range-percent)]
                            before:bg-[var(--range-active)]
                            before:pointer-events-none
                            before:content-[\'\']
                            before:z-0

                            disabled:before:opacity-0

                            [&::-webkit-slider-thumb]:relative
                            [&::-webkit-slider-thumb]:z-10
                            [&::-webkit-slider-thumb]:appearance-none
                            [&::-webkit-slider-thumb]:border
                            [&::-webkit-slider-thumb]:border-zinc-300
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
                            [&::-moz-range-thumb]:size-5
                            [&::-moz-range-thumb]:bg-white
                            [&::-moz-range-thumb]:rounded-full
                            [&::-moz-range-thumb]:shadow
                            [&::-moz-range-thumb]:transition-colors
                            [&::-moz-range-thumb]:duration-150
                        ',
                        match ($size) {
                            'xs' => 'h-2 text-xs rounded-md before:rounded-md [&::-webkit-slider-thumb]:size-3.5 [&::-moz-range-thumb]:size-3.5',
                            'sm' => 'h-2.5 text-sm rounded-md before:rounded-md [&::-webkit-slider-thumb]:size-4 [&::-moz-range-thumb]:size-4',
                            default => 'h-3 text-base rounded-lg before:rounded-lg [&::-webkit-slider-thumb]:size-4.5 [&::-moz-range-thumb]:size-4.5',
                            'lg' => 'h-3.5 text-lg rounded-lg before:rounded-lg [&::-webkit-slider-thumb]:size-5 [&::-moz-range-thumb]:size-5',
                            'xl' => 'h-4 text-xl rounded-lg before:rounded-lg [&::-webkit-slider-thumb]:size-5.5 [&::-moz-range-thumb]:size-5.5',
                            '2xl' => 'h-4.5 text-2xl rounded-xl before:rounded-xl [&::-webkit-slider-thumb]:size-6 [&::-moz-range-thumb]:size-6',
                            '3xl' => 'h-5 text-3xl rounded-xl before:rounded-xl [&::-webkit-slider-thumb]:size-6.5 [&::-moz-range-thumb]:size-6.5',
                        },
                    )
            }}
        />

        @if ($slot->hasActualContent() || $ticks)
            <div {{ TALLKit::attributesAfter($attributes, 'ticks:')->classes('flex justify-between') }}>
                @foreach (collect($ticks) as $tick)
                    <tk:slider.tick
                        :attributes="TALLKit::attributesAfter($attributes, 'tick:')->merge(is_array($tick) ? $tick : ['label' => $tick], false)"
                    />
                @endforeach

                {{ $slot }}
            </div>
        @endif
    </div>
</tk:field.wrapper>
