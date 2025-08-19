<div {{ $attributes->classes(
    'flex isolate -space-x-2 rtl:space-x-reverse',
    '*:not-first:-ml-2 **:data-tallkit-avatar:ring-white **:data-tallkit-avatar:dark:ring-zinc-800',
) }}>
    {{ $slot }}
</div>
