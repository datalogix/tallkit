@aware(['size'])
@props([
    'size' => null,
    'color' => null,
    'value' => null,
    'href' => null,
    'prepend' => null,
    'append' => null,
    'actions' => null,
])
<tk:element
    name="status"
    :$href
    :attributes="TALLKit::attributesAfter($attributes, 'container:')
        ->classes(
            '
                justify-center
                text-center

                transition
                duration-300
                [print-color-adjust:exact]
            ',
            TALLKit::roundedSize(size: $size, mode: 'large'),
            TALLKit::padding(size: $size, mode: 'largest'),
            match ($color) {
                'red' => 'bg-red-400/20 dark:bg-red-400/40 [&:is(a)]:hover:bg-red-400/30 dark:[&:is(a)]:hover:bg-red-400/50',
                'orange' => 'bg-orange-400/20 dark:bg-orange-400/40 [&:is(a)]:hover:bg-orange-400/30 dark:[&:is(a)]:hover:bg-orange-400/50',
                'amber' => 'bg-amber-400/20 dark:bg-amber-400/40 [&:is(a)]:hover:bg-amber-400/30 dark:[&:is(a)]:hover:bg-amber-400/50',
                'yellow' => 'bg-yellow-400/20 dark:bg-yellow-400/40 [&:is(a)]:hover:bg-yellow-400/30 dark:[&:is(a)]:hover:bg-yellow-400/50',
                'lime' => 'bg-lime-400/20 dark:bg-lime-400/40 [&:is(a)]:hover:bg-lime-400/30 dark:[&:is(a)]:hover:bg-lime-400/50',
                'green' => 'bg-green-400/20 dark:bg-green-400/40 [&:is(a)]:hover:bg-green-400/30 dark:[&:is(a)]:hover:bg-green-400/50',
                'emerald' => 'bg-emerald-400/20 dark:bg-emerald-400/40 [&:is(a)]:hover:bg-emerald-400/30 dark:[&:is(a)]:hover:bg-emerald-400/50',
                'teal' => 'bg-teal-400/20 dark:bg-teal-400/40 [&:is(a)]:hover:bg-teal-400/30 dark:[&:is(a)]:hover:bg-teal-400/50',
                'cyan' => 'bg-cyan-400/20 dark:bg-cyan-400/40 [&:is(a)]:hover:bg-cyan-400/30 dark:[&:is(a)]:hover:bg-cyan-400/50',
                'sky' => 'bg-sky-400/20 dark:bg-sky-400/40 [&:is(a)]:hover:bg-sky-400/30 dark:[&:is(a)]:hover:bg-sky-400/50',
                'blue' => 'bg-blue-400/20 dark:bg-blue-400/40 [&:is(a)]:hover:bg-blue-400/30 dark:[&:is(a)]:hover:bg-blue-400/50',
                'indigo' => 'bg-indigo-400/20 dark:bg-indigo-400/40 [&:is(a)]:hover:bg-indigo-400/30 dark:[&:is(a)]:hover:bg-indigo-400/50',
                'violet' => 'bg-violet-400/20 dark:bg-violet-400/40 [&:is(a)]:hover:bg-violet-400/30 dark:[&:is(a)]:hover:bg-violet-400/50',
                'purple' => 'bg-purple-400/20 dark:bg-purple-400/40 [&:is(a)]:hover:bg-purple-400/30 dark:[&:is(a)]:hover:bg-purple-400/50',
                'fuchsia' => 'bg-fuchsia-400/20 dark:bg-fuchsia-400/40 [&:is(a)]:hover:bg-fuchsia-400/30 dark:[&:is(a)]:hover:bg-fuchsia-400/50',
                'pink' => 'bg-pink-400/20 dark:bg-pink-400/40 [&:is(a)]:hover:bg-pink-400/30 dark:[&:is(a)]:hover:bg-pink-400/50',
                'rose' => 'bg-rose-400/20 dark:bg-rose-400/40 [&:is(a)]:hover:bg-rose-400/30 dark:[&:is(a)]:hover:bg-rose-400/50',
                default => 'bg-zinc-400/20 dark:bg-zinc-400/40 [&:is(a)]:hover:bg-zinc-400/30 dark:[&:is(a)]:hover:bg-zinc-400/50',
            },
        )
    "
>
    <tk:content
        :attributes="$attributes->whereDoesntStartWith(['container:', 'value:'])->classes('flex-col justify-center items-center')"
        :$size
        :$append
        :$actions
    >
        <x-slot:prepend>
            {{ $prepend }}

            <tk:heading
                :attributes="TALLKit::attributesAfter($attributes, 'value:')
                    ->classes(
                        'block w-full',
                        match ($size) {
                            'xs' => 'text-lg',
                            'sm' => 'text-xl',
                            default => 'text-2xl',
                            'lg' => 'text-3xl',
                            'xl' => 'text-4xl',
                            '2xl' => 'text-5xl',
                            '3xl' => 'text-6xl',
                        }
                    )
                "
                :variant="$color"
                :label="$value"
            />
        </x-slot:prepend>

        {{ $slot }}
    </tk:content>
</tk:element>
