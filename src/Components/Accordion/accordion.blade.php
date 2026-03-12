<div
    {{ $attributes->classes(
        $roundedSize(size: $size),
        'divide-y divide-zinc-800/20 dark:divide-white/20',
        match ($bordered) {
            true => 'border border-zinc-800/20 dark:border-white/20',
            default => '',
        }
    ) }}
    x-cloak
    x-data="disclosureGroup(@js($exclusive ?? false))"
>
    {{ $slot }}
</div>
