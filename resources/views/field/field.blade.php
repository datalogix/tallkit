@props([
    'inline' => null,
    'align' => null,
])
<div
    {{
        $attributes
            ->dataKey('field')
            ->classes('min-w-0')
            ->when(
                $inline,
                fn ($attrs) => $attrs->classes(
                    '
                        grid gap-x-3 gap-y-1.5
                        [&:has([data-tallkit-control][disabled])>[data-tallkit-label]]:opacity-50
                    ',
                    match ($align) {
                        'justify-right', 'justify-left' => 'grid-cols-[1fr_auto]',
                        default => 'grid-cols-[auto_1fr]',
                    },
                    match (true) {
                        default => '
                            [&>[data-tallkit-control]]:col-start-1
                            [&>[data-tallkit-control]]:row-start-1
                            [&>[data-tallkit-label]]:col-start-2
                            [&>[data-tallkit-label]]:row-start-1
                            [&>[data-tallkit-text]]:col-start-2
                            [&>[data-tallkit-text]]:row-start-2
                        ',
                        $align === 'right' || $align === 'justify-right' => '
                            [&>[data-tallkit-control]]:col-start-2
                            [&>[data-tallkit-control]]:row-span-2
                            [&>[data-tallkit-label]]:col-start-1
                            [&>[data-tallkit-label]]:row-start-1
                            [&>[data-tallkit-text]]:col-start-1
                            [&>[data-tallkit-text]]:row-start-2
                        ',
                    },
                ),
                fn ($attrs) => $attrs->classes(
                    '
                        [&>[data-tallkit-label]]:mb-2 [&>[data-tallkit-label]:has(+[data-tallkit-text])]:mb-1.5
                        [&>[data-tallkit-label]+[data-tallkit-text]]:mt-0
                        [&>[data-tallkit-label]+[data-tallkit-text]]:mb-2
                        [&>*:not([data-tallkit-label])+[data-tallkit-text]]:mt-2
                        [&:not(:has([data-tallkit-field])):has([data-tallkit-control][disabled])>[data-tallkit-label]]:opacity-50
                        [&_[data-tallkit-error]]:mt-1.5
                    ',
                )
            )
    }}
>
    {{ $slot }}
</div>
