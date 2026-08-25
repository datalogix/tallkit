@props([
    'size' => null,
    'mode' => null,
    'variant' => null,
])
<tk:element.wrapper
    name="heading"
    as="p"
    :attributes="$attributes->classes(
        TALLKit::fontSize(size: $size, mode: $mode ?? 'largest', weight: true),
        '[:where(&)]:w-fit [&:has(+[data-tallkit-text])]:mb-2 [[data-tallkit-text]+&]:mt-2',
        match ($variant) {
            'none' => '',
            'accent' => 'text-[var(--color-accent-content)]',
            'strong' => '[:where(&)]:text-zinc-900 dark:[:where(&)]:text-white',
            'subtle' => '[:where(&)]:text-zinc-500 dark:[:where(&)]:text-white/70',
            default => TALLKit::text($variant) ?? '[:where(&)]:text-zinc-800 dark:[:where(&)]:text-white/90',
        },
    )"
>
    {{ $slot }}
</tk:element.wrapper>
