<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :$label
    field:wire:ignore
>
    <div
        wire:ignore
        x-data="composer(@js($submit), @js($placeholder ? __($placeholder) : null))"
        role="group"
        @if ($invalid) aria-invalid="true" data-invalid @endif
        @if ($inline) data-inline @endif
        {{
            $attributes->whereDoesntStartWith([
                'field:', 'label:', 'info:', 'badge:', 'description:', 'help:', 'error:',
                'group:', 'prefix:', 'suffix:', 'append:', 'loading:',
                'header:', 'input:', 'textarea:', 'footer:', 'actionsLeading:', 'actionsTrailing:',
            ])
            ->classes(
                match ($size) {
                    'xs' => 'p-1.5 text-xs rounded-md',
                    'sm' => 'p-1.5 text-sm rounded-md',
                    default => 'p-2 text-base rounded-lg',
                    'lg' => 'p-2 text-lg rounded-lg',
                    'xl' => 'p-2.5 text-xl rounded-lg',
                    '2xl' => 'p-2.5 text-2xl rounded-xl',
                    '3xl' => 'p-3 text-3xl rounded-xl',
                },
                '
                    grid grid-cols-[auto_1fr_1fr_auto]

                    peer
                    w-full
                    appearance-none

                    shadow-xs
                    [&[disabled]]:shadow-none

                    border
                    border-zinc-200 border-b-zinc-300/80 dark:border-white/10
                    [&[disabled]]:border-b-zinc-200 dark:[&[disabled]]:border-white/5
                    [&[disabled][data-invalid]]:border-red-500 dark:[&[disabled][data-invalid]]:border-red-400
                    [&[data-invalid]]:border-red-500 dark:[&[data-invalid]]:border-red-400

                    text-zinc-700
                    [&[disabled]]:text-zinc-500

                    dark:text-zinc-300
                    dark:[&[disabled]]:text-zinc-400

                    bg-white
                    dark:bg-white/10

                    [&[disabled]]:opacity-75
                    dark:[&[disabled]]:opacity-50

                    [&[disabled]]:pointer-events-none
                '
            )
        }}
    >
        @isset ($header)
            <div {{ $attributesAfter('header:')->classes('col-span-3 flex items-center gap-1 mb-2') }}>
                {{ $header }}
            </div>
        @endisset

        <div {{ $attributesAfter('input:')->classes('
            col-span-4
            [[data-inline]_&]:col-span-2
            [[data-inline]_&]:col-start-2

            [&_[data-tallkit-control]]:h-auto
            [&_[data-tallkit-control]]:p-0
            [&_[data-tallkit-control]]:bg-transparent
            [&_[data-tallkit-control]]:border-none
            [&_[data-tallkit-control]]:focus:outline-none
            [&_[data-tallkit-control]]:resize-none

            [&_[data-tallkit-input-append]]:pe-0
            [&_[data-tallkit-textarea-append]]:top-0
            [&_[data-tallkit-textarea-append]]:right-0
        ') }}>
            @isset ($input)
                {{ $input }}
            @else
                <tk:textarea
                    :$id
                    :$size
                    :$loading
                    :$wireTarget
                    :attributes="$attributesAfter('textarea:')"
                    :label="false"
                    :max-rows="$maxRows ?? 10"
                    :rows="$inline ? 1 : ($rows ?? 2)"
                >{{ $slot }}</tk:textarea>
            @endisset
        </div>

        @isset ($footer)
            <div {{ $attributesAfter('footer:')->classes('col-span-3 flex items-center gap-1 mt-2') }}>
                {{ $footer }}
            </div>
        @endisset

        <div {{ $attributesAfter('actionsLeading:')->classes('col-span-2 [[data-inline]_&]:col-span-1 [[data-inline]_&]:col-start-1 [[data-inline]_&]:row-start-1 flex items-start gap-1') }}>
            {{ $actionsLeading ?? '' }}
        </div>

        <div {{ $attributesAfter('actionsTrailing:')->classes('col-span-2 [[data-inline]_&]:col-span-1 flex items-start justify-end gap-1') }}>
            {{ $actionsTrailing ?? '' }}
        </div>
    </div>
</tk:field.wrapper>
