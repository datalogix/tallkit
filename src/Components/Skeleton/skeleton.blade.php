@aware(['animate'])
@props(['animate'])

<div {{ $attributes->classes(
    '[:where(&)]:w-full [:where(&)]:rounded [:where(&)]:bg-zinc-400/20 my-1',
    match ($animate) {
        'shimmer' => '
            relative before:absolute before:inset-0 before:-translate-x-full
            overflow-hidden isolate
            before:z-10 before:animate-[shimmer_2s_infinite]
            before:bg-gradient-to-r before:from-transparent
            before:via-white/50 dark:before:via-zinc-900/50
            before:to-transparent
        ',
        'pulse' => 'animate-pulse',
        default => '',
    }
) }}>
    {{ $slot }}
</div>
