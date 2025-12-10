<div {{ $attributesAfter('scrollable:')->classes('overflow-x-auto overflow-y-hidden') }}>
    <div
        {{
            $attributes
                ->whereDoesntStartWith(['scrollable:'])
                ->classes(
                    'flex h-10',
                    match($variant) {
                        'pills' => 'gap-4',
                        'segmented' => 'p-1 rounded-lg bg-zinc-800/10 dark:bg-white/10',
                        default => 'gap-4 border-b border-zinc-800/10 dark:border-white/20'
                    }
                )
        }}
        role="tablist"
        x-modelable="selected"
    >
        {{ $slot }}
    </div>
</div>
