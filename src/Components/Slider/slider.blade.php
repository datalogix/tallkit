<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :$label
>
    <div
        x-data="slider"
        {{ $attributesAfter('slider:')->classes('w-full block space-y-1.5') }}
    >
        <input
            type="range"
            {{ $buildDataAttribute('control') }}
            {{ $buildDataAttribute('group-target') }}
            @isset ($name) name="{{ $name }}" @endisset
            @isset ($id) id="{{ $id }}" @endisset
            @if ($invalid) aria-invalid="true" data-invalid @endif
            {{
                $attributes->whereDoesntStartWith([
                    'field:', 'label:', 'info:', 'badge:', 'description:', 'help:', 'error:',
                    'group:', 'prefix:', 'suffix:',
                    'slider:', 'ticks:', 'tick:',
                ])->classes(
                    '
                        [--range-active:rgb(0_0_0)]
                        dark:[--range-active:rgb(255_255_255)]

                        [--range-base:rgb(0_0_0_/_.1)]
                        dark:[--range-base:rgb(255_255_255_/_.1)]

                        relative
                        block
                        w-full
                        appearance-none

                        bg-[var(--range-base)]
                        before:absolute
                        before:inset-y-0
                        before:left-0
                        before:w-[var(--range-percent)]
                        before:bg-[var(--range-active)]
                        before:pointer-events-none
                        before:content-[\'\']
                        before:z-0

                        text-zinc-700
                        disabled:text-zinc-500

                        dark:text-zinc-300
                        dark:disabled:text-zinc-400

                        disabled:opacity-25
                        dark:disabled:opacity-50

                        border
                        border-transparent
                        [&[data-invalid]]:border-red-500 dark:[&[data-invalid]]:border-red-400

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
            <div {{ $attributesAfter('ticks:')->classes('flex justify-between') }}>
                @foreach (collect($ticks) as $tick)
                    <tk:slider.tick :attributes="$attributesAfter('tick:')->merge($tick, false)" />
                @endforeach

                {{ $slot }}
            </div>
        @endif
    </div>
</tk:field.wrapper>
