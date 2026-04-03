<div {{ $attributes->classes(
    'min-w-0',
    match ($variant) {
        default => '
            [&>[data-tallkit-label]]:mb-2 [&>[data-tallkit-label]:has(+[data-tallkit-text])]:mb-1.5
            [&>[data-tallkit-label]+[data-tallkit-text]]:mt-0
            [&>[data-tallkit-label]+[data-tallkit-text]]:mb-2
            [&>*:not([data-tallkit-label])+[data-tallkit-text]]:mt-2
            [&:not(:has([data-tallkit-field])):has([data-tallkit-control][disabled])>[data-tallkit-label]]:opacity-50
            [&_[data-tallkit-error]]:mt-1.5
        ',
        'inline' => '',
    },
) }}>
    @if ($variant === 'inline')
        <div {{ $attributesAfter('inline:')->classes(
            '
                inline-grid gap-x-3 gap-y-1.5
                [&>[data-tallkit-control]~[data-tallkit-text]]:row-start-2 [&>[data-tallkit-control]~[data-tallkit-text]]:col-start-2
                [&>[data-tallkit-control]~[data-tallkit-error]]:col-span-2 [&>[data-tallkit-control]~[data-tallkit-error]]:mt-1
                [&>[data-tallkit-label]~[data-tallkit-control]]:row-start-1 [&>[data-tallkit-label]~[data-tallkit-control]]:col-start-2
                [&:not(:has([data-tallkit-field])):has([data-tallkit-control][disabled])>[data-tallkit-label]]:opacity-50
            ',

            match ($align) {
                /*'justify-left', 'justify-right' => 'grid',
                'right', 'justify-right' => '
                    h1as-[[data-tallkit-loading]~[data-tallkit-label]~[data-tallkit-control]]:grid-cols-[auto_1fr_auto]


                    [&>[data-tallkit-loading]]:row-start-1
                    [&>[data-tallkit-loading]]:col-start-3

                ',*/
                default => '
                    has-[[data-tallkit-control]~[data-tallkit-label]:not([data-tallkit-loading])]:grid-cols-[auto_1fr]
                    has-[[data-tallkit-control]~[data-tallkit-label]~[data-tallkit-loading]]:grid-cols-[auto_1fr_auto]
                ',
                /*'right' => '
                    has-[[data-tallkit-label]~[data-tallkit-control]]:grid-cols-[1fr_auto]

                    has-[[data-tallkit-label]~[data-tallkit-control]:not([data-tallkit-loading])]:grid-cols-[1fr_auto]
                    has-[[data-tallkit-label]~[data-tallkit-control]~[data-tallkit-loading]]:grid-cols-[auto_1fr_auto]
                ',*/
            },
        ) }}>
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</div>
