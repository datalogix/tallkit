<div
    popover="manual"
    role="tooltip"
    aria-hidden="true"
    {{
        $attributes->whereDoesntStartWith(['kbd:', 'arrow:'])->classes(
            'group relative overflow-visible text-white border border-white/10',
            match ($variant) {
                'accent' => 'bg-[var(--color-accent)] text-[var(--color-accent-foreground)]',
                default => 'bg-zinc-800 dark:bg-zinc-700',
                'red' => 'bg-red-600 dark:bg-red-700',
                'orange' => 'bg-orange-600 dark:bg-orange-700',
                'amber' => 'bg-amber-600 dark:bg-amber-700',
                'yellow' => 'bg-yellow-600 dark:bg-yellow-700',
                'lime' => 'bg-lime-600 dark:bg-lime-700',
                'green' => 'bg-green-600 dark:bg-green-700',
                'emerald' => 'bg-emerald-600 dark:bg-emerald-700',
                'teal' => 'bg-teal-600 dark:bg-teal-700',
                'cyan' => 'bg-cyan-600 dark:bg-cyan-700',
                'sky' => 'bg-sky-600 dark:bg-sky-700',
                'blue' => 'bg-blue-600 dark:bg-blue-700',
                'indigo' => 'bg-indigo-600 dark:bg-indigo-700',
                'violet' => 'bg-violet-600 dark:bg-violet-700',
                'purple' => 'bg-purple-600 dark:bg-purple-700',
                'fuchsia' => 'bg-fuchsia-600 dark:bg-fuchsia-700',
                'pink' => 'bg-pink-600 dark:bg-pink-700',
                'rose' => 'bg-rose-600 dark:bg-rose-700',
            },
            match ($size) {
                'xs' => 'py-1 px-1.5 text-[10px] font-light rounded',
                'sm' => 'py-1.5 px-2 text-[11px] font-normal rounded',
                default => 'py-2 px-2.5 text-xs font-medium rounded-md',
                'lg' => 'py-2 px-2.5 text-sm font-semibold rounded-md',
                'xl' => 'py-2.5 px-3 text-base font-bold rounded-lg',
                '2xl' => 'py-2.5 px-3 text-lg font-extrabold rounded-lg',
                '3xl' => 'py-3 px-3.5 text-xl font-black rounded-xl',
            },
        )
    }}
>
    <div class="flex gap-1.5">
        <div class="flex-1">
            {{ $slot }}
        </div>

        @if ($kbd)
            <span {{ $attributesAfter('kbd:')->classes('ps-1 text-zinc-300') }}>
                {{ $kbd }}
            </span>
        @endif
    </div>

    @if ($arrow)
        <tk:icon
            name="{{ is_string($arrow) ? $arrow : 'typcn:arrow-sorted-down' }}"
            :attributes="$attributesAfter('arrow:')->classes(
                '
                    absolute w-5 h-5 pointer-events-none

                    group-data-[position=top]:left-1/2
                    group-data-[position=top]:-translate-x-1/2
                    group-data-[position=top]:-bottom-3
                    group-data-[position=top]:rotate-0
                    group-data-[position=top]:mb-px

                    group-data-[position=bottom]:left-1/2
                    group-data-[position=bottom]:-translate-x-1/2
                    group-data-[position=bottom]:-top-3
                    group-data-[position=bottom]:rotate-180
                    group-data-[position=bottom]:mt-px

                    group-data-[position=left]:top-1/2
                    group-data-[position=left]:-translate-y-1/2
                    group-data-[position=left]:-right-3
                    group-data-[position=left]:rotate-270
                    group-data-[position=left]:mr-px

                    group-data-[position=right]:top-1/2
                    group-data-[position=right]:-translate-y-1/2
                    group-data-[position=right]:-left-3
                    group-data-[position=right]:rotate-90
                    group-data-[position=right]:ml-px
                ',
                match ($variant) {
                    'accent' => 'text-[var(--color-accent)]',
                    'red' => 'text-red-600 dark:text-red-500',
                    'orange' => 'text-orange-600 dark:text-orange-500',
                    'amber' => 'text-amber-600 dark:text-amber-500',
                    'yellow' => 'text-yellow-600 dark:text-yellow-500',
                    'lime' => 'text-lime-600 dark:text-lime-500',
                    'green' => 'text-green-600 dark:text-green-500',
                    'emerald' => 'text-emerald-600 dark:text-emerald-500',
                    'teal' => 'text-teal-600 dark:text-teal-500',
                    'cyan' => 'text-cyan-600 dark:text-cyan-500',
                    'sky' => 'text-sky-600 dark:text-sky-500',
                    'blue' => 'text-blue-600 dark:text-blue-500',
                    'indigo' => 'text-indigo-600 dark:text-indigo-500',
                    'violet' => 'text-violet-600 dark:text-violet-500',
                    'purple' => 'text-purple-600 dark:text-purple-500',
                    'fuchsia' => 'text-fuchsia-600 dark:text-fuchsia-500',
                    'pink' => 'text-pink-600 dark:text-pink-500',
                    'rose' => 'text-rose-600 dark:text-rose-500',
                    default => 'text-zinc-800 dark:text-zinc-700',
                },
            )"
        />
    @endif
</div>
