@props([
    'size' => null,
])
<div
    {{
        $attributes
            ->dataKey('container')
            ->classes(
                'mx-auto w-full px-4 lg:px-6',
                match ($size) {
                    'xs' => '[:where(&)]:max-w-xl',
                    'sm' => '[:where(&)]:max-w-2xl',
                    'md' => '[:where(&)]:max-w-4xl',
                    'lg' => '[:where(&)]:max-w-5xl',
                    'xl' => '[:where(&)]:max-w-6xl',
                    '2xl' => '[:where(&)]:max-w-7xl',
                    '3xl' => '[:where(&)]:max-w-[96rem]',
                    'full' => '[:where(&)]:max-w-none',
                    default => '[:where(&)]:max-w-7xl',
                },
            )
    }}
>
    {{ $slot }}
</div>
