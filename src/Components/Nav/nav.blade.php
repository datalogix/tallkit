<nav {{
    $attributes
        ->classes('flex')
        ->when(
            $list,
            fn($attrs) => $attrs->classes('gap-1 flex-col overflow-visible min-h-auto'),
            fn($attrs) => $attrs->classes(['gap-2.5', 'items-center py-3', 'overflow-x-auto overflow-y-hidden' => $scrollable]),
        )
}}>
    {{ $slot }}
</nav>
