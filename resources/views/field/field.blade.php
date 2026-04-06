@props([
    'variant' => null,
    'align' => null,
])
<div
    {{
        $attributes
            ->dataKey('field')
            ->classes(
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
            )
    }}
>
    @if ($variant === 'inline')
        <div {{ TALLKit::attributesAfter($attributes, 'inline:')->classes(
            '
                gap-x-3 gap-y-1.5
                [&:has([data-tallkit-control][disabled])>[data-tallkit-label]]:opacity-50
                grid-cols-[1fr_auto]
            ',
            match ($align) {
                'justify-left', 'justify-right' => 'grid',
                default => 'inline-grid',
            },
            match (true) {
                default => '
                    [&>[data-tallkit-control]]:col-start-1 [&>[data-tallkit-control]]:row-start-1
                    [&>[data-tallkit-label]]:col-start-2 [&>[data-tallkit-label]]:row-start-1
                    [&>[data-tallkit-text]]:col-start-2 [&>[data-tallkit-text]]:row-start-2
                ',
                $align === 'right' || $align === 'justify-right' => '
                    [&>[data-tallkit-control]]:col-start-2 [&>[data-tallkit-control]]:row-span-2
                    [&>[data-tallkit-label]]:col-start-1 [&>[data-tallkit-label]]:row-start-1
                    [&>[data-tallkit-text]]:col-start-1 [&>[data-tallkit-text]]:row-start-2
                ',
            },
        ) }}>
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</div>
