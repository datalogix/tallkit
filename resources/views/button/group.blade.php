@props([
    'size' => null,
])
<div
    {{
        $attributes
            ->dataKey('button-group')
            ->whereDoesntStartWith(['variant', 'size', 'circle', 'square'])
            ->classes(
                'flex group/button',

                '[&>[data-tallkit-input]:last-child:not(:first-child)>[data-tallkit-group-target]:not([data-invalid])]:border-s-0',
                '[&>[data-tallkit-input]:not(:first-child):not(:last-child)>[data-tallkit-group-target]:not([data-invalid])]:border-s-0',
                '[&>[data-tallkit-input]:has(+[data-tallkit-input-group-suffix])>[data-tallkit-group-target]:not([data-invalid])]:border-e-0',
                '[&>[data-tallkit-input-group-prefix]+[data-tallkit-input]>[data-tallkit-group-target]:not([data-invalid])]:border-s-0',

                '[&>*:last-child:not(:first-child)_[data-tallkit-group-target]:not([data-invalid])]:border-s-0',
                '[&>*:not(:first-child):not(:last-child)_[data-tallkit-group-target]:not([data-invalid])]:border-s-0',
                '[&>*:has(+[data-tallkit-input-group-suffix])_[data-tallkit-group-target]:not([data-invalid])]:border-e-0',
                '[&>[data-tallkit-input-group-prefix]+*_[data-tallkit-group-target]:not([data-invalid])]:border-s-0',

                '[&>[data-tallkit-group-target]:last-child:not(:first-child)]:border-s-0',
                '[&>[data-tallkit-group-target]:not(:first-child):not(:last-child)]:border-s-0',
                '[&>[data-tallkit-group-target]:has(+[data-tallkit-input-group-suffix])]:border-e-0',
                '[&>[data-tallkit-input-group-prefix]+[data-tallkit-group-target]]:border-s-0',

                '[&>[data-tallkit-group-target]:not(:first-child):not(:last-child)]:rounded-none',
                '[&>[data-tallkit-group-target]:first-child:not(:last-child)]:rounded-e-none',
                '[&>[data-tallkit-group-target]:last-child:not(:first-child)]:rounded-s-none',

                '[&>*:not(:first-child):not(:last-child):not(:only-child)_[data-tallkit-group-target]]:rounded-none',
                '[&>*:first-child:not(:last-child)_[data-tallkit-group-target]]:rounded-e-none',
                '[&>*:last-child:not(:first-child)_[data-tallkit-group-target]]:rounded-s-none',
            )
    }}
>
    {{ $slot }}
</div>
