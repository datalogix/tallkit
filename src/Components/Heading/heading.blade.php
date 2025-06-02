@if ($slot->isNotEmpty() || $text)
    @php $level = intval($level); @endphp
    <{{ $level > 0 && $level < 5 ? 'h' . $level : 'div' }} {{ $attributes->classes(
            'font-medium',
            match ($accent) {
                true => 'text-[var(--color-accent-content)]',
                default => 'text-zinc-800 dark:text-white',
            },
            match ($size) {
                '3xl' => 'text-4xl',
                '2xl' => 'text-3xl',
                'xl' => 'text-2xl',
                'lg' => 'text-xl',
                default => 'text-lg',
                'sm' => 'text-base',
            },
            '[&:has(+[data-tallkit-text])]:mb-2 [[data-tallkit-text]+&]:mt-2'
        ) }} data-tallkit-heading>
        {{ $slot->isEmpty() ? __($text) : $slot }}
    </{{ $level > 0 && $level < 5 ? 'h' . $level : 'div' }}>
@endif
