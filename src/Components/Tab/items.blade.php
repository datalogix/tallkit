<div {{ $attributesAfter('scrollable:')->classes('overflow-x-auto overflow-y-hidden') }}>
    <div
        {{
            $attributes
                ->whereDoesntStartWith(['scrollable:'])
                ->classes(match($variant) {
                    'pills' => 'flex h-8 gap-4 w-full',
                    'segmented' => 'inline-flex h-10 p-1 rounded-lg bg-zinc-800/10 dark:bg-white/10',
                    default => 'flex h-10 gap-4 border-b border-zinc-800/10 dark:border-white/20'
                })
        }}
        role="tablist"
        x-modelable="selected"
    >
        {{ $slot }}
    </div>
</div>
