@props([
    'size' => null,
])
<div
    aria-hidden="true"
    {{
        $attributes->classes(
            'h-px flex-1 bg-zinc-300 dark:bg-zinc-500',
            TALLKit::generateClassBySize(size: $size, name: 'mt', values: ['3', '3.5', '4', '4.5', '5', '6', '8']),
        )
    }}
></div>
