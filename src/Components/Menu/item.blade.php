<tk:element
    :name="$baseComponentKey()"
    :as="$as ?? 'button'"
    :icon:size="$adjustSize()"
    :icon:class="'me-1.5'"
    :icon-dot:class="'me-1'"
    :icon-trailing:size="$adjustSize()"
    :icon-trailing:class="'ms-1.5 text-zinc-400 [[data-tallkit-menu-item-icon-trailing]:hover_&]:text-current'"
    :badge:size="$adjustSize()"
    :badge:class="'ms-2'"
    :content:class="$classes('flex-1 leading-none whitespace-nowrap', $fontSize($size, true))"
    :attributes="$attributes->classes(
        '
        rounded-md
        px-2.5 py-2
        w-full focus:outline-hidden
        text-start
        [[disabled]_&]:opacity-50 [&[disabled]]:opacity-50

        *:[data-tallkit-menu-item-icon]:text-zinc-400
        dark:*:[data-tallkit-menu-item-icon]:text-white/60
        [&[data-active]_[data-tallkit-menu-item-icon]]:text-current

        *:[data-tallkit-menu-item-icon-trailing]:text-zinc-400
        dark:*:[data-tallkit-menu-item-icon-trailing]:text-white/60
        [&[data-active]_[data-tallkit-menu-item-icon-trailing]]:text-current
        ',
        match ($variant) {
            'danger' => 'text-zinc-500 data-active:text-red-500 data-active:bg-red-50 dark:text-white/80 dark:data-active:bg-red-400/20 dark:data-active:text-red-200',
            default => 'text-zinc-500 dark:text-white/80 data-active:text-zinc-800 dark:data-active:text-white data-active:bg-zinc-100 dark:data-active:bg-white/10',
        },
    )->merge(['data-keep-open' => $keepOpen, 'role' => 'menuitem'])"
>
    <x-slot:icon-empty>
        <div class="w-7 hidden [[data-tallkit-menu]:has(>[data-tallkit-menu-item-has-icon])_&]:block"></div>
    </x-slot:icon-empty>

    {{ $slot }}
</tk:element>
