<div {{ $attributes->classes(
    'flex group/button',

    /* All inputs borders... */
    '[&>[data-tallkit-input]:last-child:ot(:first-child)>[data-tallkit-group-target]:not([data-invalid])]:border-s-0',
    '[&>[data-tallkit-input]:not(:first-child):not(:last-child)>[data-tallkit-group-target]:not([data-invalid])]:border-s-0',
    '[&>[data-tallkit-input]:has(+[data-tallkit-input-group-suffix])>[data-tallkit-group-target]:not([data-invalid])]:border-e-0',

    /* Selects and date pickers borders... */
    '[&>*:last-child:not(:first-child)>[data-tallkit-group-target]:not([data-invalid])]:border-s-0',
    '[&>*:not(:first-child):not(:last-child)>[data-tallkit-group-target]:not([data-invalid])]:border-s-0',
    '[&>*:has(+[data-tallkit-input-group-suffix])>[data-tallkit-group-target]:not([data-invalid])]:border-e-0',

    /* Buttons borders... */
    '[&>[data-tallkit-group-target]:last-child:not(:first-child)]:border-s-0',
    '[&>[data-tallkit-group-target]:not(:first-child):not(:last-child)]:border-s-0',
    '[&>[data-tallkit-group-target]:has(+[data-tallkit-input-group-suffix])]:border-e-0',

    /* "Weld" the borders of inputs together by overriding their border radiuses... */
    '[&>[data-tallkit-group-target]:not(:first-child):not(:last-child)]:rounded-none',
    '[&>[data-tallkit-group-target]:first-child:not(:last-child)]:rounded-e-none',
    '[&>[data-tallkit-group-target]:last-child:not(:first-child)]:rounded-s-none',

    /* "Weld" borders for sub-children of group targets (button element inside ui-select element, etc.)... */
    '[&>*:not(:first-child):not(:last-child):not(:only-child)>[data-tallkit-group-target]]:rounded-none',
    '[&>*:first-child:not(:last-child)>[data-tallkit-group-target]]:rounded-e-none',
    '[&>*:last-child:not(:first-child)>[data-tallkit-group-target]]:rounded-s-none',

    /* "Weld" borders for sub-sub-children of group targets (input element inside div inside ui-select element (combobox))... */
    '[&>*:not(:first-child):not(:last-child):not(:only-child)>[data-tallkit-input]>[data-tallkit-group-target]]:rounded-none',
    '[&>*:first-child:not(:last-child)>[data-tallkit-input]>[data-tallkit-group-target]]:rounded-e-none',
    '[&>*:last-child:not(:first-child)>[data-tallkit-input]>[data-tallkit-group-target]]:rounded-s-none',
)->whereDoesntStartWith(['variant', 'size', 'circle', 'square']) }}>
    {{ $slot }}
</div>
