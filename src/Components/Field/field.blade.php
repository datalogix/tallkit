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
                has-[[data-tallkit-control]~[data-tallkit-label]]:grid-cols-[auto_1fr]
                [&>[data-tallkit-control]~[data-tallkit-text]]:row-start-2 [&>[data-tallkit-control]~[data-tallkit-text]]:col-start-2
                [&>[data-tallkit-control]~[data-tallkit-error]]:col-span-2 [&>[data-tallkit-control]~[data-tallkit-error]]:mt-1
                [&>[data-tallkit-label]~[data-tallkit-control]]:row-start-1 [&>[data-tallkit-label]~[data-tallkit-control]]:col-start-2
                [&:not(:has([data-tallkit-field])):has([data-tallkit-control][disabled])>[data-tallkit-label]]:opacity-50
            ',
            match ($align) {
                'left', 'right' => 'has-[[data-tallkit-label]~[data-tallkit-control]]:grid-cols-[auto_1fr]',
                'justify-right' => 'grid has-[[data-tallkit-label]~[data-tallkit-control]]:grid-cols-[1fr_auto]',
                'justify-left' => 'grid has-[[data-tallkit-control]~[data-tallkit-label]]:grid-cols-[1fr_auto]',
                default => '',
            },
        ) }}>
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</div>
