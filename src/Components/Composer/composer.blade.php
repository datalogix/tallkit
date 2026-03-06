<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :$label
>
    <div
        wire:ignore
        x-data="composer(@js($submit), @js($placeholder ? __($placeholder) : null))"
        role="group"
        @if ($invalid) aria-invalid="true" data-invalid @endif
        @if ($inline) data-inline @endif
        {{
            $attributes->whereDoesntStartWith([
                'field:', 'label:', 'info:', 'badge:', 'description:',
                'group:', 'prefix:', 'suffix:',
                'help:', 'error:',
                'control:', 'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:',
                'header:', 'input:', 'textarea:', 'footer:', 'actions-leading:', 'actions-trailing:',
            ])
            ->classes(
                $fontSize(size: $size),
                $roundedSize(size: $size, mode: 'large'),
                $padding(size: $size, mode: 'smallest'),
                '
                    grid grid-cols-[auto_1fr_1fr_auto]

                    peer
                    w-full
                    appearance-none

                    outline-none
                    focus-within:ring-2
                    focus-within:ring-[Highlight]

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
            <div {{ $attributesAfter('header:')->classes(
                '
                    flex items-center gap-1
                    mb-2
                    col-span-3
                '
            ) }}>
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
        ') }}>
            <tk:field.control
                :$attributes
                :$size
            >
                @isset ($input)
                    {{ $input }}
                @else
                    <tk:textarea
                        :$id
                        :$size
                        :attributes="$attributesAfter('textarea:')"
                        :label="false"
                        :max-rows="$maxRows ?? 10"
                        :rows="$inline ? 1 : ($rows ?? 2)"
                    >{{ $slot }}</tk:textarea>
                @endisset
            </tk:field.control>
        </div>

        @isset ($footer)
            <div {{ $attributesAfter('footer:')->classes(
                '
                    flex items-center gap-1
                    mt-2
                    col-span-3
                '
            ) }}>
                {{ $footer }}
            </div>
        @endisset

        @isset ($actionsLeading)
            <div {{ $attributesAfter('actions-leading:')->classes(
                '
                    flex items-start gap-1
                    col-span-2
                    [[data-inline]_&]:col-span-1
                    [[data-inline]_&]:col-start-1
                    [[data-inline]_&]:row-start-1
                '
            ) }}>
                {{ $actionsLeading ?? '' }}
            </div>
        @endisset

        @isset ($actionsTrailing)
            <div {{ $attributesAfter('actions-trailing:')->classes(
                '
                    flex items-start justify-end gap-1
                    col-span-2
                    [[data-inline]_&]:col-span-1
                '
            ) }}>
                {{ $actionsTrailing ?? '' }}
            </div>
        @endisset
    </div>
</tk:field.wrapper>
