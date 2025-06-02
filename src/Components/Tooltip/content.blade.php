<div {{ $attributes
    ->whereDoesntStartWith(['kbd:'])
    ->classes(
        'relative',
        'py-2 px-2.5',
        'rounded-md',
        'text-xs text-white font-medium',
        'bg-zinc-800',
        'dark:bg-zinc-700 dark:border dark:border-white/10',
        'overflow-visible'
    )
}}
    popover="manual" role="tooltip" aria-hidden="true" data-tallkit-tooltip-content>
    <div class="flex gap-1.5">
        <div class="flex-1">
            {{ $slot }}
        </div>

        @if ($kbd)
            <span {{ $attributesAfter('kbd:')->classes('ps-1 text-zinc-300') }}>
                {{ $kbd }}
            </span>
        @endif
    </div>
</div>
