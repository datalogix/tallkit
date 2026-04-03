<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :$label
    :prefix="null"
    :suffix="null"
    label:as="{{ $attributes->has('label:for') ? 'label' : 'span' }}"
>
    <div {{ $attributes->only('class')->classes(
        'w-full flex',
        '[&_[data-tallkit-field]]:grow',
        '[&_[data-tallkit-input]]:grow',
        '[&_[data-tallkit-label]]:hidden',

        // All inputs borders...
        '[&>[data-tallkit-input]:last-child:not(:first-child)>[data-tallkit-group-target]:not([data-invalid])]:border-s-0',
        '[&>[data-tallkit-input]:not(:first-child):not(:last-child)>[data-tallkit-group-target]:not([data-invalid])]:border-s-0',
        '[&>[data-tallkit-input]:has(+[data-tallkit-input-group-suffix])>[data-tallkit-group-target]:not([data-invalid])]:border-e-0',


        // Selects and date pickers borders...
        '[&>*:last-child:not(:first-child)_[data-tallkit-group-target]:not([data-invalid])]:border-s-0',
        '[&>*:not(:first-child):not(:last-child)_[data-tallkit-group-target]:not([data-invalid])]:border-s-0',
        '[&>*:has(+[data-tallkit-input-group-suffix])_[data-tallkit-group-target]:not([data-invalid])]:border-e-0',

        // Buttons borders...
        '[&>[data-tallkit-group-target]:last-child:not(:first-child)]:border-s-0',
        '[&>[data-tallkit-group-target]:not(:first-child):not(:last-child)]:border-s-0',
        '[&>[data-tallkit-group-target]:has(+[data-tallkit-input-group-suffix])]:border-e-0',

        // "Weld" the borders of inputs together by overriding their border radiuses...
        '[&>[data-tallkit-group-target]:not(:first-child):not(:last-child)]:rounded-none',
        '[&>[data-tallkit-group-target]:first-child:not(:last-child)]:rounded-e-none',
        '[&>[data-tallkit-group-target]:last-child:not(:first-child)]:rounded-s-none',

        // "Weld" borders for sub-children of group targets (button element inside select element, etc.)...
        '[&>*:not(:first-child):not(:last-child):not(:only-child)_[data-tallkit-group-target]]:rounded-none',
        '[&>*:first-child:not(:last-child)_[data-tallkit-group-target]]:rounded-e-none',
        '[&>*:last-child:not(:first-child)_[data-tallkit-group-target]]:rounded-s-none',
    ) }}>
        @if ($prefix)
            <tk:field.group.prefix
                :attributes="$attributesAfter('prefix:')"
                :$size
            >
                {!! $prefix !!}
            </tk:field.group.prefix>
        @endif

        {{ $slot }}

        @if ($suffix)
            <tk:field.group.suffix
                :attributes="$attributesAfter('suffix:')"
                :$size
            >
                {!! $suffix !!}
            </tk:field.group.suffix>
        @endif
    </div>
</tk:field.wrapper>
