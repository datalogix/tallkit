<tk:element
    :name="$baseComponentKey()"
    as="button"
    :icon:size="$adjustSize()"
    icon:class="me-2"
    icon-dot:class="me-2"
    :icon-trailing:size="$adjustSize()"
    icon-trailing:class="ms-auto text-zinc-400 [[data-tallkit-menu-item-icon-trailing]:hover_&]:text-current"
    suffix:class="ms-auto"
    :attributes="$attributes->classes(
        '
        px-2.5 py-2
        w-full focus:outline-hidden
        text-start
        [[disabled]_&]:opacity-50 [&[disabled]]:opacity-50

        **:data-tallkit-menu-item-icon:text-zinc-400
        dark:**:data-tallkit-menu-item-icon:text-white/60
        [&[data-active]_[data-tallkit-menu-item-icon]]:text-current

        **:data-tallkit-menu-item-icon-trailing:text-zinc-400
        dark:**:data-tallkit-menu-item-icon-trailing:text-white/60
        [&[data-active]_[data-tallkit-menu-item-icon-trailing]]:text-current
        ',
        match ($size) {
            'xs' => 'text-[11px] font-normal rounded',
            'sm' => 'text-xs font-normal rounded',
            default => 'text-sm font-medium rounded-md',
            'lg' => 'text-base font-medium rounded-md',
            'xl' => 'text-lg font-semibold rounded-lg',
            '2xl' => 'text-xl font-semibold rounded-lg',
            '3xl' => 'text-2xl font-bold rounded-xl',
        },
        match ($variant) {
            'danger' => 'text-zinc-800 data-active:text-red-600 data-active:bg-red-50 dark:text-white dark:data-active:bg-red-400/20 dark:data-active:text-red-400',
            default => 'text-zinc-800 data-active:bg-zinc-50 dark:text-white dark:data-active:bg-zinc-600',
        },
    )->merge(['data-keep-open' => $keepOpen, 'role' => 'menuitem'])"
>
    <x-slot name="icon-empty">
        <div class="w-7 hidden [[data-tallkit-menu]:has(>[data-tallkit-menu-item-has-icon])_&]:block"></div>
    </x-slot>

    {{ $slot }}

    <x-slot name="append">
        {{ $submenu ?? '' }}
    </x-slot>
</tk:element>
