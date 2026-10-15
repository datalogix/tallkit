@props([
    'items' => [],
])
<tk:section
    :attributes="$attributes->whereDoesntStartWith(['link:'])->merge(['title' => 'Account', 'subtitle' => 'Manage how you sign in and keep your account safe.', 'separator' => false])"
>
    <nav class="divide-y divide-zinc-800/10 dark:divide-white/20">
        @foreach ($items as $item)
            <a
                {{ $attributes->prefixed('link:')->classes(
                    'flex items-center justify-between py-3 text-sm font-medium hover:underline',
                    ($item['danger'] ?? false) ? 'text-red-600 dark:text-red-400' : 'text-zinc-800 dark:text-white',
                ) }}
                href="{{ $item['url'] }}"
            >
                {{ __($item['label']) }}
                <tk:icon name="chevron-right" class="size-4" />
            </a>
        @endforeach
    </nav>
</tk:section>
