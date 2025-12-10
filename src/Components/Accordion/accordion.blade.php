<div
    {{ $attributes->classes('divide-y divide-zinc-800/10 dark:divide-white/10') }}
    x-cloak
    x-data="disclosureGroup(@js($exclusive ?? false))"
>
    {{ $slot }}
</div>
